/**
 * Copyright © 2020-present ParadoxLabs, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Need help? Try our knowledgebase and support system:
 * @link https://support.paradoxlabs.com
 */

define(
    [
        'ko',
        'jquery',
        'underscore',
        'ParadoxLabs_TokenBase/js/view/payment/method-renderer/cc',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/payment/additional-validators',
        'ParadoxLabs_CyberSource/js/unified-checkout-client',
        'ParadoxLabs_CyberSource/js/payer-auth-client',
        'mage/translate'
    ],
    function (ko, $, _, Component, quote, additionalValidators, ucClient, payerAuthClient) {
        'use strict';
        var config = window.checkoutConfig.payment.paradoxlabs_cybersource;
        // How long after a mount is kicked off to verify the drop-in actually painted. The UC iframe
        // loads asynchronously and exposes no documented ready event, so this is a wall-clock check.
        var MOUNT_HEALTH_CHECK_MS = 3000;
        // Outer bound on a whole mount cycle (request -> UC.js load -> mount). The library load is the
        // one leg that can stall without ever calling back — neither onReady nor onError — which would
        // leave the in-flight guard set and every future mount attempt blocked. Arming the health check
        // when the cycle opens means that stall is caught and retried like any other dead mount.
        var CYCLE_TIMEOUT_MS = 45000;
        // Placeholder card id used to flag a freshly tokenized (not-yet-vaulted) card to the place-order UI.
        var NEW_CARD_ID = 'unified_checkout_new';
        // Consecutive-failure cap on mount attempts. Quote observables (billing address, payment method)
        // notify on many checkout actions; without a latch a persistent failure (e.g. UC.js blocked by
        // CSP) turns every notification into a fresh signed capture-context API call, indefinitely.
        var MAX_MOUNT_FAILURES = 3;

        // ---------------------------------------------------------------------------------------------
        // Iter-3 additional_data contract (getData()):
        //   New card  => transient_token = <UC transient-token JWT>, card_id = null (empty).
        //   Stored card => transient_token = null, card_id = <vault hash>.
        // The synthetic NEW_CARD_ID placeholder is normalized to null here; it never reaches the
        // server as a real card_id. Exactly one of {transient_token, card_id} is populated per submit.
        // ---------------------------------------------------------------------------------------------
        return Component.extend({
            defaults: {
                template: 'ParadoxLabs_CyberSource/payment/unified-checkout',
                save: config ? config.canSaveCard && config.defaultSaveCard : false,
                selectedCard: config ? config.selectedCard : '',
                storedCards: config ? config.storedCards : [],
                logoImage: config ? config.logoImage : false,
                transientToken: null,
                lastGrandTotal: null
            },
            initVars: function () {
                this.canSaveCard = config ? config.canSaveCard : false;
                this.forceSaveCard = config ? config.forceSaveCard : false;
                this.defaultSaveCard = config ? config.defaultSaveCard : false;
                this.requireCcv = config ? config.requireCcv : false;
            },
            initObservable: function () {
                this.initVars();
                this._super()
                    .observe([
                        'transientToken'
                    ]);

                this.storedCards = ko.observableArray(config.storedCards);

                // In-flight capture-context jqXHR; tracked so rapid total/card changes can abort a
                // stale request instead of double-mounting.
                this._captureXhr = null;

                // Mount cycle bookkeeping. A cycle spans request -> library load -> mount -> paint, all
                // async, and the jqXHR handle only covers the first leg: it is nulled the moment the ajax
                // call settles, leaving the load/mount/paint legs unguarded. A quote notification landing
                // in that window used to slip every guard and start a second concurrent cycle, whose
                // response would then clobber the first's capture context — and the context carries the
                // capture mandate's amount, so the drop-in could end up bound to a different total than
                // the order being placed. _activeGeneration marks a cycle in flight for its whole life;
                // _mountGeneration invalidates superseded cycles so their late callbacks are discarded.
                this._mountGeneration = 0;
                this._activeGeneration = null;

                // Expiry timers, deliberately split. The capture context and the transient token have
                // separate lifetimes and separate consequences: a lapsed context only matters while the
                // customer has yet to tokenize (silent re-mount, nothing to lose), whereas a lapsed token
                // means discarding card entry the customer already completed, which must be announced.
                // A single mount-armed timer conflated the two and silently wiped in-progress checkouts.
                this._contextTimer = null;
                this._tokenTimer = null;
                this._healthTimer = null;

                // Consecutive mount/load failures; latches maybeMountDropin() at MAX_MOUNT_FAILURES.
                this._mountFailures = 0;

                // Last seen selectedCard value. The TokenBase cc.js base fires
                // selectedCard.notifySubscribers() on a 100ms interval (form-validation UX), so the
                // subscription receives constant no-change notifications; the handler must only react
                // to actual transitions or every tick becomes a mount attempt / latch reset.
                this._lastSelectedCard = this.selectedCard();

                // Payer authentication (3-D Secure 2) state machine. The pre-place sequence runs inside
                // the placeOrder() override; these fields drive its latch/re-entry discipline.
                //   _payerAuthCleared     - this place attempt has passed payer auth; placeOrder()
                //                           delegates straight to the base (the parent placeOrder()).
                //   _payerAuthInFlight    - the sequence is running; blocks a second concurrent run
                //                           (double-click, or auto-place racing a manual click).
                //   _payerAuthGeneration  - stamps each sequence so a teardown (resetPayerAuthState)
                //                           invalidates in-flight continuations; mirrors the
                //                           mount-generation idiom used for the drop-in above.
                //   _reverifyAttempted    - at most one automatic re-auth after a server "verify again"
                //                           refusal, per instrument (reset on any card/token change).
                //   _activeChallenge      - the in-flight runChallenge() handle, so a mid-challenge
                //                           remount can cancel the modal (task: drop-in remount).
                this._payerAuthCleared = false;
                this._payerAuthInFlight = false;
                this._payerAuthGeneration = 0;
                this._reverifyAttempted = false;
                this._activeChallenge = null;

                // Real-change detection for the transientToken subscription, mirroring _lastSelectedCard:
                // the observable is re-notified on the base class's 100ms interval, so the handler must
                // act on actual value transitions only.
                this._lastTransientToken = this.transientToken();

                // Capture subscription handles so dispose() can tear them down; on checkout region
                // re-render the component is recreated and these would otherwise accumulate (N x handlers).
                this._subscriptions = [
                    quote.billingAddress.subscribe(this.maybeMountDropin.bind(this)),
                    quote.paymentMethod.subscribe(this.maybeMountDropin.bind(this)),
                    this.selectedCard.subscribe(this.handleSelectedCardChange.bind(this)),
                    // Re-authenticate against a freshly entered card: a new transient token is a new
                    // instrument, so any cleared payer-auth latch from a prior instrument must drop.
                    this.transientToken.subscribe(this.handleTransientTokenChange.bind(this)),
                    // Re-request the capture context whenever the grand total changes, so the amount in
                    // the capture mandate stays in sync with the order being placed.
                    quote.totals.subscribe(this.handleTotalChange.bind(this)),
                    // Keep the place button visibly disabled through the whole payer-auth sequence:
                    // the base placeOrder's .always() re-enables it mid-recovery during the silent
                    // re-verify. Clicks are swallowed by _payerAuthInFlight either way.
                    this.isPlaceOrderActionAllowed.subscribe(this.holdPlaceOrderDuringPayerAuth.bind(this))
                ];

                this.showDropin = ko.computed(function () {
                    return (this.selectedCard() === null || this.selectedCard() === undefined)
                           && quote.billingAddress() !== null;
                }, this);

                this.showSaveOption = ko.computed(function () {
                    if (this.canSaveCard !== true) {
                        return false;
                    }

                    // No card selected means the customer is entering a new card in the drop-in;
                    // the save choice renders alongside it so consent happens BEFORE tokenization
                    // (auto-place submits straight from the drop-in, with no pause to ask after).
                    if (this.selectedCard() === null
                        || this.selectedCard() === undefined
                        || this.selectedCard() === '') {
                        return true;
                    }

                    var cards = this.storedCards();
                    for (var key in cards) {
                        if (cards[key].id === this.selectedCard()) {
                            return cards[key].new;
                        }
                    }

                    return false;
                }, this);

                this.useVault = ko.computed(function () {
                    return this.storedCards().length > 0;
                }, this);

                this.loadFingerprint();

                return this;
            },

            /**
             * Inject the CyberSource Decision Manager (online-metrix) device-fingerprint tag.
             *
             * Legacy parity: the SA/SOAP renderer loaded this so DM could collect the device signal keyed on
             * the per-quote session id. Guarded against duplicate injection because the checkout region can
             * re-render this component; the tag only needs to load once per page.
             */
            loadFingerprint: function () {
                if (config.fingerprintUrl === undefined
                    || config.fingerprintUrl === null
                    || config.fingerprintUrl.length <= 1) {
                    return;
                }

                // De-dupe by URL: a re-render must not append a second identical tag.
                if (document.querySelector('script[data-cybs-fingerprint="' + config.fingerprintUrl + '"]')) {
                    return;
                }

                // Bypassing requireJS because this is easy enough and bypasses core .min-ifying.
                var script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = config.fingerprintUrl;
                script.setAttribute('data-cybs-fingerprint', config.fingerprintUrl);
                document.head.appendChild(script);
            },

            /**
             * Mount the Unified Checkout drop-in when we are the active method, have a billing address,
             * are adding a new card, and the drop-in is not already mounted/in-flight.
             *
             * The guard keys on actual mount health rather than a sticky boolean, so a stored -> new card
             * transition reliably re-mounts (a sticky latch would dead-end).
             *
             * The failure latch stops the quote subscriptions from re-requesting forever when the
             * mount cannot succeed (e.g. UC.js blocked); an explicit "Add new card" re-selection
             * resets it (handleSelectedCardChange) so a transient outage stays recoverable.
             */
            maybeMountDropin: function () {
                var screen = $('#' + this.getCode() + '_uc_screen');

                if (quote.paymentMethod() === null
                    || quote.paymentMethod().method !== this.getCode()
                    || this.selectedCard()
                    || quote.billingAddress() === null
                    || screen.length === 0
                    || this._activeGeneration !== null
                    || this._mountFailures >= MAX_MOUNT_FAILURES
                    || this.isDropinMounted()) {
                    return;
                }

                this.requestCaptureContext();
            },

            /**
             * Whether the drop-in is mounted AND usable.
             *
             * UC renders in two shapes depending on the merchant's enabled payment types: card-only
             * configurations mount the card form straight into paymentScreen, but with wallets enabled
             * (Google Pay etc.) it mounts a buttonlist into paymentSelection and leaves paymentScreen
             * EMPTY until the customer clicks "Checkout with card". Either container holding healthy
             * content is a live mount; checking only the screen reads the buttonlist shape as dead,
             * so the health check would remount forever (leaking Accept instances and burning signed
             * capture-context calls) and then blame the "failed" form at the customer.
             *
             * Health per container is delegated to ucClient: children present, and not visible-at-
             * zero-height (the 0x0 dead-iframe case; hidden-but-healthy legitimately measures zero).
             *
             * @return {Boolean}
             */
            isDropinMounted: function () {
                return ucClient.isContainerHealthy($('#' + this.getCode() + '_uc_selection'))
                    || ucClient.isContainerHealthy($('#' + this.getCode() + '_uc_screen'));
            },

            /**
             * React to a card-selection change.
             *
             * - Stored card selected (drop-in hidden): reset captured state + empty the containers so
             *   returning to "Add new card" re-requests a fresh capture context and re-mounts (C1).
             * - "Add new card" selected (empty): attempt a mount.
             * - Synthetic NEW_CARD_ID (a freshly captured token): do nothing, so the capture does not
             *   re-trigger the mount path (I3).
             */
            handleSelectedCardChange: function () {
                var selected = this.selectedCard();

                // Ignore the base class's 100ms notifySubscribers() ticks; act on real changes only.
                if (selected === this._lastSelectedCard) {
                    return;
                }

                this._lastSelectedCard = selected;

                // The selected instrument changed for real: any payer-auth clearance belonged to the
                // previous card/token, so drop it (and settle any in-flight sequence/challenge) before
                // the next placeOrder() re-authenticates the new instrument.
                this.resetPayerAuthState();

                if (selected === NEW_CARD_ID) {
                    return;
                }

                if (selected) {
                    // A real stored card is now selected; abandon any pending/mounted drop-in.
                    this.resetDropinState();

                    return;
                }

                // Explicit return to "Add new card": clear the failure latch so a past transient
                // failure does not dead-end a deliberate retry.
                this._mountFailures = 0;

                // Deliberately re-entering a card after already tokenizing one; drop the captured token
                // and its timer so the abandoned token cannot outlive the entry it came from.
                if (this.transientToken()) {
                    this.resetDropinState();
                }

                this.maybeMountDropin();
            },

            /**
             * Request a capture-context JWT from the frontend endpoint, then load the UC client library.
             *
             * @return {jqXHR}
             */
            requestCaptureContext: function () {
                $('#' + this.getCode() + '_uc_screen').trigger('processStart');
                this.lastGrandTotal = this.getGrandTotal();

                // Open a mount cycle. Every async callback below carries this stamp and drops out if it
                // no longer matches, so a superseded cycle cannot mount or hand back a token.
                var generation = ++this._mountGeneration;
                this._activeGeneration = generation;

                // Bound the whole cycle from here; mountUnifiedCheckout() re-arms this at the shorter
                // paint interval once the mount is actually underway.
                this.scheduleMountHealthCheck(CYCLE_TIMEOUT_MS);

                this._captureXhr = $.post({
                    url: config.captureContextUrl,
                    dataType: 'json',
                    data: this.getCaptureContextParams(),
                    global: false,
                    success: function (data) {
                        this.loadClientLibrary(data, generation);
                    }.bind(this),
                    error: this.handleAjaxError.bind(this)
                });

                // Only the ajax leg is done here; the cycle stays open through load and mount, so this
                // releases the abort handle but deliberately not the in-flight guard.
                this._captureXhr.always(function () {
                    this._captureXhr = null;
                }.bind(this));

                return this._captureXhr;
            },

            /**
             * Whether an async callback belongs to the current mount cycle. A stale callback is one whose
             * cycle was superseded (a new request) or torn down (resetDropinState) while it was in flight.
             *
             * @param {Number} generation
             * @return {Boolean}
             */
            isCurrentGeneration: function (generation) {
                return generation === this._mountGeneration;
            },

            /**
             * Decode the capture-context JWT, inject the UC client library (with SRI), then mount the drop-in.
             *
             * The capture context is threaded through the chain as an argument rather than stashed on the
             * instance: it carries the capture mandate's amount, and a shared property would let a late
             * response from a superseded cycle overwrite the context the mounted drop-in is bound to.
             *
             * @param {Object} data
             * @param {Number} generation
             */
            loadClientLibrary: function (data, generation) {
                if (!this.isCurrentGeneration(generation)) {
                    return;
                }

                if (!data || !data.captureContext) {
                    this.handleAjaxError(null, 'error', 'Missing capture context');

                    return;
                }

                ucClient.loadClientLibrary(
                    data.captureContext,
                    this.mountUnifiedCheckout.bind(this, data.captureContext, generation),
                    function (message) {
                        // A superseded cycle's failure is not this cycle's failure: handleAjaxError
                        // mutates shared state (the in-flight guard, the failure budget), so letting a
                        // stale error through would clobber a live mount. RequireJS de-dupes loads of
                        // the same UC.js URL, so overlapping cycles share one underlying load and its
                        // failure fans out to every waiting callback — this is routine, not exotic.
                        if (!this.isCurrentGeneration(generation)) {
                            return;
                        }

                        this.handleAjaxError(null, 'error', message);
                    }.bind(this)
                );
            },

            /**
             * Mount the UC drop-in via the Accept global into the embedded containers.
             *
             * @param {String} captureContext
             * @param {Number} generation
             */
            mountUnifiedCheckout: function (captureContext, generation) {
                if (!this.isCurrentGeneration(generation)) {
                    return;
                }

                if (!ucClient.isAvailable()) {
                    this.handleAjaxError(null, 'error', 'Payment library unavailable');

                    return;
                }

                ucClient.mountUnifiedPayments(
                    captureContext,
                    '#' + this.getCode() + '_uc_selection',
                    '#' + this.getCode() + '_uc_screen'
                )
                    .then(function (transientTokenJwt) {
                        // A token from a superseded mount belongs to a dead context; never accept it.
                        if (!this.isCurrentGeneration(generation)) {
                            return;
                        }

                        this.handleTransientToken(transientTokenJwt);
                    }.bind(this))
                    .catch(function (error) {
                        // As above: a superseded mount's rejection must not fail the live cycle.
                        if (!this.isCurrentGeneration(generation)) {
                            return;
                        }

                        this.handleAjaxError(null, 'error', error && error.message ? error.message : null);
                    }.bind(this));

                // NB: the latch is deliberately NOT cleared here. A mount being *underway* proves nothing
                // — the 0-height failure mode reaches exactly this point every time — and clearing on
                // attempt would let checkMountHealth()'s increment be zeroed before the next check could
                // ever observe a second consecutive failure, so the latch could never trip. It is cleared
                // once a mount is confirmed healthy instead.
                $('#' + this.getCode() + '_uc_screen').trigger('processStop');
                this.scheduleContextRefresh(captureContext);
                this.scheduleMountHealthCheck(MOUNT_HEALTH_CHECK_MS);
            },

            /**
             * Store the resolved transient-token JWT and flag a new card so the place-order UI activates.
             *
             * @param {String} transientTokenJwt
             */
            handleTransientToken: function (transientTokenJwt) {
                if (!transientTokenJwt) {
                    return;
                }

                this.transientToken(transientTokenJwt);

                // Card entry is done: the capture context has served its purpose and the token's own
                // lifetime takes over from here. A pending health check is likewise moot.
                this.clearTimer('_contextTimer');
                this.clearTimer('_healthTimer');
                this.scheduleTokenExpiry(transientTokenJwt);

                // Surface a synthetic "new card" entry, labeled with the card identity decoded from
                // the token so the selector reads e.g. "Visa ****1111" rather than a bare "New Card".
                var metadata = ucClient.getCardMetadata(transientTokenJwt);

                this.storedCards.remove(function (card) {
                    return card.id === NEW_CARD_ID;
                });
                // Select BEFORE pushing: the push re-renders the select element, and the value
                // binding re-syncing against the new option list must find selectedCard already
                // pointing at the entry — the old push-then-select order let that re-sync write
                // undefined into selectedCard first (a race the auto-place path cannot tolerate).
                this.selectedCard(NEW_CARD_ID);
                this.storedCards.push({
                    id: NEW_CARD_ID,
                    label: this.getNewCardLabel(metadata),
                    selected: true,
                    new: true,
                    type: metadata && metadata.type ? metadata.type : '',
                    cc_bin: metadata && metadata.bin ? metadata.bin : '',
                    cc_last4: metadata && metadata.last4 ? metadata.last4 : ''
                });

                // NEW-CARD TOKENIZATION ONLY, and only from here: this handler is the sole
                // resolution point of a user-driven tokenize, and the generation stamp upstream
                // already discarded superseded cycles (remount/total-change/expiry), so this fires
                // at most once per tokenization. Stored cards keep CVV entry + manual Place Order.
                this.maybeAutoPlaceOrder();
            },

            /**
             * Place the order automatically after a fresh tokenization, when configured.
             *
             * Only when the checkout validators (agreements et al.) pass and place-order is
             * allowed; on validation failure the validators have shown their messages and the
             * customer falls back to the always-rendered manual Place Order button. A server-side
             * failure flows through handleFailedOrder like a manual submit — the consumed token
             * forces a re-mount, so there is no resubmit/auto-place loop.
             */
            maybeAutoPlaceOrder: function () {
                if (!config.autoPlaceOrder
                    || !additionalValidators.validate()
                    || !this.isPlaceOrderActionAllowed()) {
                    return;
                }

                this.placeOrder();
            },

            /**
             * A failed place order consumed the single-use transient token server-side (whether or
             * not the gateway was reached, it cannot be trusted for a resubmit). Surface the base
             * error, then force a re-mount so the customer re-enters against a fresh capture
             * context instead of resubmitting a dead token. This is a deliberate user-visible
             * reset, not a mount failure: the NEW_CARD_ID -> null transition clears the failure
             * latch, and nothing here increments it, so MAX_MOUNT_FAILURES cannot trip from a
             * declined order — and auto-place cannot loop, since it only refires after the
             * customer completes card entry again.
             */
            handleFailedOrder: function (response) {
                // Server re-verify path (PA-1 BindingValidator::reverify): the order already passed
                // CLIENT payer auth this attempt (latch set), yet the server refused because the
                // persisted authentication no longer covers the charge (stale/incomplete/mismatched).
                // That refusal fires BEFORE the gateway is reached, so the instrument is intact — re-run
                // the payer-auth sequence once automatically, reusing the same token/card, then re-place.
                // A second refusal falls through to the base error. Reset per-instrument via
                // resetPayerAuthState. NB: skipped here on purpose is the drop-in remount below, so the
                // token is preserved for the re-auth.
                if (this._payerAuthCleared === true
                    && this._reverifyAttempted !== true
                    && this.isReverifyFailure(response)) {
                    this._reverifyAttempted = true;
                    this._payerAuthCleared = false;
                    this.runPayerAuth();

                    return;
                }

                // Terminal failure (hard decline, or a second re-verify refusal): drop the payer-auth
                // latch so the NEXT place re-authenticates. This must run for stored cards too — they
                // have no transient token, so the remount below (which is what resets the latch for a
                // new card) never fires, and without this an amount-mismatch loop would leave
                // _payerAuthCleared/_reverifyAttempted stuck true and every retry would delegate
                // straight to the base and be refused identically, forever.
                this._payerAuthCleared = false;
                this._reverifyAttempted = false;

                // Re-mount before the base error alert: the base handler parses the response body
                // and can throw on a bodyless failure (network drop), which must not leave the
                // dead token in place.
                if (this.transientToken()) {
                    this.remountDropin();
                }

                this._super(response);
            },

            /**
             * Whether a failed place response is the server's payer-auth "verify again" refusal.
             *
             * Matches BindingValidator::REVERIFY_MARKER, pinned by BindingValidatorTest so drift
             * fails the suite (the webapi fault carries no machine-readable code). A blanket
             * re-auth on any failure would be wrong: a genuine decline consumes the single-use
             * transient token, so only this refusal — which precedes the gateway — is retried.
             *
             * @param {Object} response - the failed place jqXHR-like response
             * @return {Boolean}
             */
            isReverifyFailure: function (response) {
                var message = '';

                try {
                    if (response && response.responseText) {
                        var body = JSON.parse(response.responseText);

                        message = typeof body.message === 'string' ? body.message : '';
                    }
                } catch (e) {
                    return false;
                }

                return message.indexOf('verify your payment again') !== -1;
            },

            /**
             * Place-order funnel with a payer-authentication (3DS2) pre-step.
             *
             * This overrides the base checkout placeOrder() — the one method every place path funnels
             * through: the manual button (click: placeOrder), the auto-place path after tokenization
             * (maybeAutoPlaceOrder), and stored-card submits. Payer auth must fully resolve before the
             * order is placed, so:
             *   - latch set (_payerAuthCleared) => delegate straight to the base placeOrder. This
             *     synchronous _super() is the ONLY point _super is valid: Magento's UI-component _super
             *     is unavailable from a promise callback, so the async sequence re-ENTERS placeOrder()
             *     to reach this branch rather than calling _super() from a continuation.
             *   - a sequence already running (_payerAuthInFlight) => no-op, so a double-click or an
             *     auto-place racing a manual click cannot start two sequences.
             *   - otherwise run the payer-auth sequence (runPayerAuth), which re-enters here on success.
             *
             * The base's own validate()/additionalValidators/isPlaceOrderActionAllowed gate is applied
             * up front so a payer-auth round-trip (and a possible challenge modal) is never spent on an
             * order that would not place — e.g. before required agreements are checked.
             *
             * @param {Object} [data]
             * @param {Object} [event]
             * @return {Boolean}
             */
            placeOrder: function (data, event) {
                if (event) {
                    event.preventDefault();
                }

                if (this._payerAuthCleared === true) {
                    return this._super(data, event);
                }

                // Payer Authentication off for this store: place exactly as before payer auth
                // existed, with no setup round-trip. This is ONLY a cost optimization, so it must
                // fail toward running auth: gate on an EXPLICIT false, never on a missing key. A
                // stale checkoutConfig bundle, a 404'd payer-auth-client.js, or a tab opened before
                // the merchant enabled 3DS all leave the flag undefined -- in which case we run
                // setup and let the server answer 'skipped' (one round-trip), rather than silently
                // placing an unauthenticated order.
                if (config.payerAuthActive === false) {
                    return this._super(data, event);
                }

                if (this._payerAuthInFlight === true) {
                    return false;
                }

                if (!this.validate()
                    || !additionalValidators.validate()
                    || this.isPlaceOrderActionAllowed() !== true) {
                    return false;
                }

                this.runPayerAuth();

                return false;
            },

            /**
             * Run the pre-place payer-authentication sequence for the current instrument.
             *
             * setup -> (skipped ? place) : DDC (best-effort) -> authenticate -> success/failed/challenge.
             * Each step is stamped with a generation so a teardown (resetPayerAuthState — card change,
             * remount, dispose) mid-flight discards the continuation instead of placing a stale order.
             * The place button is disabled for the duration and restored on every terminal path.
             */
            runPayerAuth: function () {
                var self = this;
                var payload = this.buildPayerAuthPayload();

                if (payload === null) {
                    // No instrument to authenticate (validators should have blocked this); fail safe.
                    return;
                }

                var generation = ++this._payerAuthGeneration;
                this._payerAuthInFlight = true;
                this.isPlaceOrderActionAllowed(false);

                payerAuthClient.setup(payload)
                    .then(function (setupResult) {
                        if (!self.isCurrentPayerAuth(generation)) {
                            return null;
                        }

                        // PA disabled server-side / excluded type / legacy card: proceed exactly as
                        // today. This keeps PA-off behavior identical (aside from the one setup call).
                        if (setupResult && setupResult.skipped === true) {
                            return self.completePayerAuth(generation);
                        }

                        var accessToken = payerAuthClient.getResultField(
                            setupResult,
                            'accessToken',
                            'access_token'
                        );
                        var ddcUrl = payerAuthClient.getResultField(
                            setupResult,
                            'deviceDataCollectionUrl',
                            'device_data_collection_url'
                        );

                        // Device data collection is best-effort and always resolves; proceed regardless.
                        return payerAuthClient.runDdc(accessToken, ddcUrl)
                            .then(function () {
                                if (!self.isCurrentPayerAuth(generation)) {
                                    return null;
                                }

                                return self.runAuthenticate(generation);
                            });
                    })
                    .catch(function (error) {
                        if (!self.isCurrentPayerAuth(generation)) {
                            return;
                        }

                        self.handlePayerAuthError(error);
                    });
            },

            /**
             * Build the setup payload: exactly one of {transientToken} (new card) or {cardHash}
             * (stored card), mirroring getData()'s additional_data contract.
             *
             * @return {Object|null} null when there is no instrument to authenticate
             */
            buildPayerAuthPayload: function () {
                var selected = this.selectedCard();
                var token = this.transientToken();

                // New card (synthetic NEW_CARD_ID or nothing selected yet): the freshly tokenized token.
                if (selected === NEW_CARD_ID || !selected) {
                    return token ? {transientToken: token} : null;
                }

                // A real stored card is selected: authenticate by its vault hash.
                return {cardHash: selected};
            },

            /**
             * Step 2: enrollment check, then branch on the outcome.
             *
             * @param {Number} generation
             * @return {Promise}
             */
            runAuthenticate: function (generation) {
                var self = this;
                var browserInfo = payerAuthClient.collectBrowserInfo();

                return payerAuthClient.authenticate(browserInfo)
                    .then(function (result) {
                        if (!self.isCurrentPayerAuth(generation)) {
                            return null;
                        }

                        return self.handleAuthenticateResult(result, generation);
                    });
            },

            /**
             * Route an authenticate/finalize outcome: success/skipped => place; failed => decline;
             * challenge => run the issuer step-up.
             *
             * @param {Object} result - {status, acsUrl|acs_url, pareq}
             * @param {Number} generation
             * @return {Promise|undefined}
             */
            handleAuthenticateResult: function (result, generation) {
                var status = result && result.status;

                if (status === 'success' || status === 'skipped') {
                    return this.completePayerAuth(generation);
                }

                if (status === 'challenge') {
                    return this.runChallengeFlow(result, generation);
                }

                // 'failed' or anything unexpected: hard decline. The card is not the problem shape, so
                // the drop-in is deliberately NOT reset — the server obligation blocks placement anyway.
                this.declinePayerAuth();
            },

            /**
             * Run the issuer challenge, then finalize. Holds the challenge handle so a mid-challenge
             * remount can cancel the modal (resetPayerAuthState).
             *
             * @param {Object} authResult - carries acsUrl|acs_url and pareq
             * @param {Number} generation
             * @return {Promise}
             */
            runChallengeFlow: function (authResult, generation) {
                var self = this;
                var acsUrl = payerAuthClient.getResultField(authResult, 'acsUrl', 'acs_url');
                var pareq = authResult ? authResult.pareq : '';
                var challenge = payerAuthClient.runChallenge(acsUrl, pareq);

                this._activeChallenge = challenge;

                return challenge.promise.then(function (challengeResult) {
                    if (!self.isCurrentPayerAuth(generation)) {
                        // Superseded (e.g. a remount cancelled the challenge): the teardown owns cleanup.
                        return null;
                    }

                    self._activeChallenge = null;

                    if (challengeResult.status !== 'return') {
                        // Cancelled / timeout / error: re-enable checkout, no order.
                        return self.cancelPayerAuth(challengeResult.status);
                    }

                    // The step-up returned; the real outcome is read server-side from CyberSource.
                    return payerAuthClient.finalize().then(function (finalizeResult) {
                        if (!self.isCurrentPayerAuth(generation)) {
                            return null;
                        }

                        return self.handleAuthenticateResult(finalizeResult, generation);
                    });
                }).catch(function (error) {
                    if (!self.isCurrentPayerAuth(generation)) {
                        return;
                    }

                    self._activeChallenge = null;
                    self.handlePayerAuthError(error);
                });
            },

            /**
             * Re-disable the place button if something re-enabled it while payer auth is still running.
             *
             * Terminal paths clear _payerAuthInFlight before restoring the button, so only the base
             * placeOrder's .always() triggers this.
             *
             * @param {Boolean} allowed
             */
            holdPlaceOrderDuringPayerAuth: function (allowed) {
                if (allowed === true && this._payerAuthInFlight === true) {
                    this.isPlaceOrderActionAllowed(false);
                }
            },

            /**
             * Whether a payer-auth continuation still belongs to the live sequence. A stale one is a
             * continuation whose sequence was superseded or torn down (resetPayerAuthState) in flight.
             *
             * @param {Number} generation
             * @return {Boolean}
             */
            isCurrentPayerAuth: function (generation) {
                return generation === this._payerAuthGeneration;
            },

            /**
             * Payer auth cleared: latch it, restore the button, and re-enter placeOrder() so the base
             * places the order synchronously (the only path where _super is valid).
             *
             * @param {Number} generation
             */
            completePayerAuth: function (generation) {
                if (!this.isCurrentPayerAuth(generation)) {
                    return;
                }

                this._payerAuthCleared = true;
                this._payerAuthInFlight = false;
                this.isPlaceOrderActionAllowed(true);

                this.placeOrder();
            },

            /**
             * Hard decline: authentication failed outright. Surface the message and re-enable checkout
             * WITHOUT resetting the drop-in — re-entering the same card cannot help, and the server
             * obligation blocks placement regardless, so a retry with the same instrument stays blocked.
             */
            declinePayerAuth: function () {
                this._payerAuthCleared = false;
                this._payerAuthInFlight = false;
                this._activeChallenge = null;
                this.isPlaceOrderActionAllowed(true);

                ucClient.showError(
                    $.mage.__('Your payment could not be verified. Please try another payment method.')
                );
            },

            /**
             * Customer-driven end to the challenge (closed the modal, timed out, or a frame error).
             * Re-enable checkout and place no order; the customer can retry.
             *
             * @param {String} status - 'cancelled'|'timeout'|'error'
             */
            cancelPayerAuth: function (status) {
                this._payerAuthCleared = false;
                this._payerAuthInFlight = false;
                this._activeChallenge = null;
                this.isPlaceOrderActionAllowed(true);

                if (status === 'timeout') {
                    ucClient.showError(
                        $.mage.__('Payment verification timed out. Please try again.')
                    );
                } else if (status === 'error') {
                    ucClient.showError(
                        $.mage.__('Payment verification could not be completed. Please try again.')
                    );
                }
                // 'cancelled' is a deliberate customer action; re-enable silently, no error banner.
            },

            /**
             * Transport/server error anywhere in the sequence. Re-enable checkout and show the message.
             *
             * @param {Error} error
             */
            handlePayerAuthError: function (error) {
                this._payerAuthCleared = false;
                this._payerAuthInFlight = false;
                this._activeChallenge = null;
                this.isPlaceOrderActionAllowed(true);

                var message = error && error.message
                    ? error.message
                    : $.mage.__('Payment authentication is unavailable. Please try again.');

                ucClient.showError(message);
            },

            /**
             * Settle and forget any in-flight payer-auth sequence, dropping the cleared latch.
             *
             * Bumping the generation neutralizes every outstanding continuation (so a resolving setup /
             * authenticate / finalize / challenge cannot place an order or mutate state afterward), then
             * the challenge modal is cancelled so a step-up open at teardown is torn down. Called on any
             * real card/token change and from resetDropinState (remount/total-change/dispose), so the
             * next placeOrder() authenticates the current instrument from scratch.
             */
            resetPayerAuthState: function () {
                var wasInFlight = this._payerAuthInFlight;

                this._payerAuthGeneration++;
                this._payerAuthCleared = false;
                this._payerAuthInFlight = false;
                this._reverifyAttempted = false;

                if (this._activeChallenge && typeof this._activeChallenge.cancel === 'function') {
                    this._activeChallenge.cancel();
                }

                this._activeChallenge = null;

                // The generation bump neutralizes the in-flight continuation that would otherwise have
                // restored the button, so re-enable it here — a sequence torn down mid-flight must not
                // leave the place button stuck disabled.
                if (wasInFlight === true) {
                    this.isPlaceOrderActionAllowed(true);
                }
            },

            /**
             * React to a real transientToken change (a newly entered card is a new instrument): drop any
             * payer-auth clearance carried over from a prior token. Ignores the base's 100ms
             * notifySubscribers churn via _lastTransientToken, matching the selectedCard idiom.
             */
            handleTransientTokenChange: function () {
                var token = this.transientToken();

                if (token === this._lastTransientToken) {
                    return;
                }

                this._lastTransientToken = token;
                this.resetPayerAuthState();
            },

            /**
             * Selector label for a freshly tokenized card: "{Brand} ****{last4}" when the token
             * carries card metadata, the wallet name for wallet tokens, else a generic "New Card".
             *
             * @param {Object|null} metadata - ucClient.getCardMetadata() result
             * @return {String}
             */
            getNewCardLabel: function (metadata) {
                if (metadata && metadata.label && metadata.last4) {
                    return $.mage.__('%1 ****%2')
                        .replace('%1', metadata.label)
                        .replace('%2', metadata.last4);
                }

                if (metadata && ucClient.getWalletLabel(metadata.paymentType)) {
                    return ucClient.getWalletLabel(metadata.paymentType);
                }

                return $.mage.__('New Card');
            },

            /**
             * Re-request the capture context if the grand total changed after the drop-in was mounted,
             * whether or not a token was already captured.
             *
             * The capture mandate/3DS amount is baked into the capture context (and into any captured
             * transient token), so a total change from a coupon or shipping update after the customer
             * finished card entry leaves that amount stale. remountDropin() -> resetDropinState() clears
             * the captured token + synthetic card, forcing re-entry against a fresh, correctly-priced
             * context. Guards: lastGrandTotal null => never mounted; nothing mounted => nothing to re-mount.
             */
            handleTotalChange: function () {
                if (this.lastGrandTotal === null
                    || !this.isDropinMounted()) {
                    return;
                }

                if (this.getGrandTotal() !== this.lastGrandTotal) {
                    this.remountDropin();
                }
            },

            /**
             * Clear a timer handle by property name, if armed.
             *
             * @param {String} handle
             */
            clearTimer: function (handle) {
                if (this[handle]) {
                    clearTimeout(this[handle]);
                    this[handle] = null;
                }
            },

            /**
             * Schedule a silent re-mount for when the capture context lapses.
             *
             * @param {String} captureContext
             */
            scheduleContextRefresh: function (captureContext) {
                this.clearTimer('_contextTimer');

                this._contextTimer = setTimeout(
                    this.handleContextExpiry.bind(this),
                    ucClient.getRefreshDelay(captureContext)
                );
            },

            /**
             * The capture context has lapsed. Nothing has been entered that a re-mount would destroy, so
             * swap in a fresh one silently — but only if no token was captured against it, in which case
             * the context is already spent and the token timer owns expiry.
             */
            handleContextExpiry: function () {
                this._contextTimer = null;

                if (this.transientToken()) {
                    return;
                }

                this.remountDropin();
            },

            /**
             * Schedule expiry of a captured transient token.
             *
             * @param {String} transientTokenJwt
             */
            scheduleTokenExpiry: function (transientTokenJwt) {
                this.clearTimer('_tokenTimer');

                this._tokenTimer = setTimeout(
                    this.handleTokenExpiry.bind(this),
                    ucClient.getRefreshDelay(transientTokenJwt)
                );
            },

            /**
             * The captured token has lapsed and the server would now reject it, so the card entry the
             * customer already completed has to go. Unlike a context refresh this destroys real work,
             * so it is always announced rather than silently wiping the form under them.
             */
            handleTokenExpiry: function () {
                this._tokenTimer = null;

                this.remountDropin();

                ucClient.showError(
                    $.mage.__('Your payment session expired. Please re-enter your card details.')
                );
            },

            /**
             * Schedule the mount health check.
             *
             * @param {Number} delay - CYCLE_TIMEOUT_MS when arming at cycle open (the library load can
             *                         stall without ever calling back), MOUNT_HEALTH_CHECK_MS once the
             *                         mount is underway and only the paint is outstanding.
             */
            scheduleMountHealthCheck: function (delay) {
                this.clearTimer('_healthTimer');

                this._healthTimer = setTimeout(this.checkMountHealth.bind(this), delay);
            },

            /**
             * Recover from a mount that attached but never became usable (the 0-height iframe case).
             *
             * This is also the only place a mount is confirmed to have actually worked, so it owns
             * clearing the failure latch. Each dead mount counts against that latch, so a persistently
             * broken mount trips it and stops rather than looping on signed capture-context calls; the
             * customer gets told once instead of being left with a silently empty payment area.
             */
            checkMountHealth: function () {
                this._healthTimer = null;

                // The mount cycle ends here however it went, so release the in-flight guard before any
                // branch below can return — a marker left set would block every future mount attempt.
                this._activeGeneration = null;

                if (this.transientToken()
                    || !$('#' + this.getCode() + '_uc_screen').is(':visible')) {
                    return;
                }

                if (this.isDropinMounted()) {
                    this._mountFailures = 0;

                    return;
                }

                this._mountFailures++;

                if (this._mountFailures >= MAX_MOUNT_FAILURES) {
                    ucClient.showError(
                        $.mage.__('The payment form could not be loaded. Please reload the page and try again.')
                    );

                    return;
                }

                this.remountDropin();
            },

            /**
             * Tear down captured state and re-request a fresh capture context + drop-in.
             */
            remountDropin: function () {
                this.resetDropinState();

                // resetDropinState() may have flipped selectedCard from NEW_CARD_ID to null, which
                // fires handleSelectedCardChange -> maybeMountDropin and already kicks off a request.
                // Only mount here if that did not happen (e.g. total-change while no token captured),
                // and let the guarded maybeMountDropin avoid a duplicate request either way.
                this.maybeMountDropin();
            },

            /**
             * Tear down any mounted/in-flight drop-in: cancel the expiry timers, abort the in-flight
             * capture-context request, drop captured token + synthetic card, and empty the containers.
             *
             * Called when switching to a stored card and as the first step of a re-mount, so a fresh
             * mount never races a stale request or double-injects into the same containers (I1).
             */
            resetDropinState: function () {
                // A remount (token TTL, total change, health recovery) can fire while a payer-auth
                // challenge is open. Tear that down first: cancel the challenge modal and invalidate any
                // in-flight sequence so its continuation cannot place an order against the discarded card.
                this.resetPayerAuthState();

                this.clearTimer('_contextTimer');
                this.clearTimer('_tokenTimer');
                this.clearTimer('_healthTimer');

                // Invalidate any cycle still in flight: bumping the generation makes its callbacks
                // no-ops, so a load or mount started before this teardown cannot land afterwards.
                this._mountGeneration++;
                this._activeGeneration = null;

                if (this._captureXhr) {
                    this._captureXhr.abort();
                    this._captureXhr = null;
                }

                this.transientToken(null);
                this.storedCards.remove(function (card) {
                    return card.id === NEW_CARD_ID;
                });
                if (this.selectedCard() === NEW_CARD_ID) {
                    this.selectedCard(null);
                }

                this.lastGrandTotal = null;

                ucClient.releaseMountObservers('#' + this.getCode() + '_uc_selection');
                $('#' + this.getCode() + '_uc_selection').empty();
                $('#' + this.getCode() + '_uc_screen').empty();

                // The discarded Accept() instances also left helper iframes on document.body,
                // out of reach of the container empties above; reap them so re-mounts don't accumulate.
                ucClient.reapOrphanedFrames();
            },

            /**
             * KO UI component teardown. Clear the expiry timers, abort any in-flight request, and dispose
             * the quote subscriptions so a re-rendered checkout region does not leave detached handlers
             * or a timer firing against a detached DOM (C2).
             */
            dispose: function () {
                // Cancel any open challenge modal and invalidate an in-flight sequence before the DOM
                // this component owns is detached.
                this.resetPayerAuthState();

                this.clearTimer('_contextTimer');
                this.clearTimer('_tokenTimer');
                this.clearTimer('_healthTimer');

                // Aborting the xhr only stops a cycle still on its ajax leg; one already past it would
                // otherwise mount into the detached DOM this component is being torn down from.
                this._mountGeneration++;
                this._activeGeneration = null;

                if (this._captureXhr) {
                    this._captureXhr.abort();
                    this._captureXhr = null;
                }

                if (this._subscriptions) {
                    this._subscriptions.forEach(function (subscription) {
                        subscription.dispose();
                    });
                    this._subscriptions = [];
                }

                this._super();
            },

            handleAjaxError: function (jqXHR, status, error) {
                // jQuery abort()s surface here with status 'abort'; that is an intentional teardown
                // (stored-card switch / re-mount), not a failure, so swallow it silently.
                if (status === 'abort') {
                    return;
                }

                this._mountFailures++;

                $('#' + this.getCode() + '_uc_screen').trigger('processStop');
                this._captureXhr = null;

                // The cycle died here; release the in-flight guard so a retry is possible.
                this._activeGeneration = null;

                var message = $.mage.__('A server error occurred. Please try again.');

                if (typeof error === 'string' && error.length > 0) {
                    message = error;
                }

                try {
                    if (jqXHR && jqXHR.responseText) {
                        var responseJson = JSON.parse(jqXHR.responseText);
                        if (responseJson.message !== undefined) {
                            message = responseJson.message;
                        }
                    }
                } catch (e) {
                    // responseText was not JSON; keep the default/passed message.
                }

                ucClient.showError(message);
            },

            /**
             * Build the POST payload for the capture-context endpoint (billing + guest email + form key).
             *
             * @return {Object}
             */
            getCaptureContextParams: function () {
                var billingAddress = _.pick(
                    quote.billingAddress(),
                    [
                        'firstname',
                        'lastname',
                        'company',
                        'street',
                        'city',
                        'regionCode',
                        'regionId',
                        'region',
                        'postcode',
                        'countryId',
                        'telephone'
                    ]
                );

                if (quote.guestEmail !== undefined && quote.guestEmail !== null) {
                    billingAddress.email = quote.guestEmail;
                }

                return {
                    'billing': billingAddress,
                    'source': 'checkout',
                    'guest_email': quote.guestEmail !== undefined ? quote.guestEmail : null,
                    'form_key': this.getFormKey()
                };
            },

            /**
             * Current quote grand total, used to detect amount changes that require a fresh capture context.
             *
             * @return {Number|null}
             */
            getGrandTotal: function () {
                var totals = quote.totals();

                return totals && totals.grand_total !== undefined ? totals.grand_total : null;
            },

            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'transient_token': this.transientToken(),
                        'card_id': this.selectedCard() === NEW_CARD_ID ? null : this.selectedCard(),
                        'cc_cid': this.creditCardVerificationNumber(),
                        'save': this.save()
                    }
                };
            },

            /**
             * Whether to collect a security code ourselves.
             *
             * Only for a real vaulted card, matching what the setting actually offers ("force customers to
             * enter CCV when using a stored card"). The drop-in collects the code as part of card entry, so
             * asking again for a freshly tokenized card is a pointless second prompt.
             *
             * @return {Boolean}
             */
            hasVerification: function () {
                var selected = this.selectedCard();

                return this.requireCcv() && !!selected && selected !== NEW_CARD_ID;
            },

            getFormKey: function () {
                return $('input[name="form_key"]').val();
            }
        });
    }
);
