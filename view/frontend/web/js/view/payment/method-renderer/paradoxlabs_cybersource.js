define(
    [
        'ko',
        'jquery',
        'underscore',
        'ParadoxLabs_TokenBase/js/view/payment/method-renderer/cc',
        'Magento_Ui/js/modal/alert',
        'Magento_Checkout/js/model/quote',
        'mage/translate'
    ],
    function (ko, $, _, Component, alert, quote) {
        'use strict';
        var config=window.checkoutConfig.payment.paradoxlabs_cybersource;
        return Component.extend({
            defaults: {
                template: 'ParadoxLabs_CyberSource/payment/secure-acceptance',
                save: config ? config.canSaveCard && config.defaultSaveCard : false,
                selectedCard: config ? config.selectedCard : '',
                storedCards: config ? config.storedCards : [],
                logoImage: config ? config.logoImage : false,
                billingAddressLine: null,
                iframeInitialized: false
            },
            initVars: function() {
                this.canSaveCard     = config ? config.canSaveCard : false;
                this.forceSaveCard   = config ? config.forceSaveCard : false;
                this.defaultSaveCard = config ? config.defaultSaveCard : false;
                this.requireCcv      = config ? config.requireCcv : false;
            },
            initObservable: function () {
                this.initVars();
                this._super()
                    .observe([
                        'billingAddressLine'
                    ]);

                quote.billingAddress.subscribe(this.syncSecureAcceptBillingAddress.bind(this));
                quote.paymentMethod.subscribe(this.syncSecureAcceptBillingAddress.bind(this));
                this.billingAddressLine.subscribe(this.initSecureAcceptanceForm.bind(this));
                this.selectedCard.subscribe(this.checkReinitSecureAcceptanceForm.bind(this));

                this.showIframe = ko.computed(function() {
                    return (this.selectedCard() === null || this.selectedCard() === undefined)
                           && quote.billingAddress() !== null;
                }, this);

                this.showSaveOption = ko.computed(function() {
                    if (this.canSaveCard !== true
                        || this.selectedCard() === null
                        || this.selectedCard() === undefined) {
                        return false;
                    }

                    for (var card of this.storedCards()) {
                        if (card.id === this.selectedCard()) {
                            return card.new;
                        }
                    }

                    return false;
                }, this);

                this.storedCards = ko.observableArray(config.storedCards);

                this.useVault = ko.computed(function() {
                    return this.storedCards().length > 0;
                }, this);

                this.loadFingerprint();
                this.loadPayerAuth();

                return this;
            },
            loadFingerprint: function() {
                if (config.fingerprintUrl !== undefined
                    && config.fingerprintUrl !== null
                    && config.fingerprintUrl.length > 1) {
                    // Bypassing requireJS because this is easy enough and bypasses core .min-ifying.
                    var script = document.createElement('script');
                    script.type = 'text/javascript';
                    script.src = config.fingerprintUrl;
                    document.head.appendChild(script);
                }
            },
            syncSecureAcceptBillingAddress: function() {
                // Don't progess until the iframe has rendered, we're the active payment method, we have a billing addr.
                if ($('#' + this.getCode() + '_iframe').length === 0
                    || quote.paymentMethod() === null
                    || quote.paymentMethod().method !== this.getCode()
                    || this.selectedCard()
                    || quote.billingAddress() === null) {
                    return;
                }

                this.billingAddressLine(this.getAddressLine(quote.billingAddress()));
            },
            checkReinitSecureAcceptanceForm: function() {
                if (this.iframeInitialized === false
                    && (this.selectedCard() === null || this.selectedCard() === undefined)
                    && this.storedCards().length > 0) {
                    // The initialized flag is to debounce and ensure we don't reinit unless absolutely necessary.
                    this.iframeInitialized = true;
                    this.initSecureAcceptanceForm();
                }
            },
            initSecureAcceptanceForm: function() {
                this.bindCommunicator();

                // Clear and spinner the CC form while we load new params
                $('#' + this.getCode() + '_iframe').prop('src', 'about:blank')
                                                   .trigger('processStart');

                var billingAddress = _.pick(
                    quote.billingAddress(),
                    [
                        'firstname',
                        'lastname',
                        'company',
                        'street',
                        'city',
                        'regionCode',
                        'region',
                        'postcode',
                        'countryId',
                        'telephone'
                    ]
                );

                if (quote.guestEmail !== undefined && quote.guestEmail !== null) {
                    billingAddress.email = quote.guestEmail;
                }

                return $.post({
                    url: config.paramUrl,
                    dataType: 'json',
                    data: {
                        'billing': billingAddress,
                        'source': 'checkout',
                        'guestEmail': quote.guestEmail !== undefined ? quote.guestEmail : null
                    },
                    global: false,
                    success: this.loadSecureAcceptanceForm.bind(this),
                    error: this.handleAjaxError.bind(this)
                });
            },
            loadSecureAcceptanceForm: function(data) {
                var form = document.createElement('form');
                form.target = this.getCode() + '_iframe';
                form.method = 'post';
                form.action = data.iframeAction;

                for (var key in data.iframeParams) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = data.iframeParams[key];
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();

                $('#' + this.getCode() + '_iframe').trigger('processStop');
            },
            handleAjaxError: function(jqXHR, status, error) {
                $('#' + this.getCode() + '_iframe').trigger('processStop');

                var message = $.mage.__('A server error occurred. Please try again.');

                try {
                    var responseJson = JSON.parse(jqXHR.responseText);
                    if (responseJson.message !== undefined) {
                        message = responseJson.message;
                    }
                } catch (error) {}

                try {
                    alert({
                        title: $.mage.__('Error'),
                        content: message
                    });
                } catch (error) {
                    // Fall back to standard alert if jq widget hasn't initialized yet
                    window.alert(message);
                }
            },
            bindCommunicator: function() {
                window.jQuery('#' + this.getCode() + '-communicator')
                    .off('change')
                    .on('change', this.handleCommunication.bind(this));
            },
            handleCommunication: function(event) {
                var value = event.target.value;

                if (value !== undefined && value !== null && value !== '') {
                    var message = JSON.parse(value);

                    if (message.success && message.card !== undefined) {
                        this.storedCards.push(message.card);
                        this.selectedCard(message.card.id);
                        this.iframeInitialized = false;
                    } else if (message.error.length > 0) {
                        this.initSecureAcceptanceForm();

                        alert({
                            title: $.mage.__('Error'),
                            content: message.error
                        });
                    }
                }
            },
            getAddressLine: function(address) {
                if (address === null) {
                    return null;
                }

                return address.firstname + ' '
                       + address.lastname + ', '
                       + address.street.join(' ') + ', '
                       + address.city + ', '
                       + address.region + ' '
                       + address.postcode + ', '
                       + address.countryId + ' '
                       + address.telephone;
            },
            loadPayerAuth: function() {
                if (config.cardinalUrl !== undefined
                    && config.cardinalUrl !== null
                    && config.cardinalUrl.length > 1) {
                    // Bypassing requireJS because this is easy enough and bypasses core .min-ifying.
                    var script = document.createElement('script');
                    script.type = 'text/javascript';
                    script.src = config.cardinalUrl;
                    script.addEventListener('load', this.initPayerAuth.bind(this));
                    document.head.appendChild(script);
                }
            },
            initPayerAuth: function() {
                Cardinal.configure({
                    logging: {
                        level: 'on' // TODO: Remove this after completion
                    },
                    payment: {
                        displayLoading: true
                    }
                });

                // TODO: Step 4 listen
                Cardinal.on('payments.setupComplete', function(data) {
                    // TODO: I don't know that I need this.
                    //  triggers when Songbird has successfully initialized, after calling Cardinal.setup()
                    console.log('caught payments.setupComplete', data);
                });
                Cardinal.on('payments.validated', this.handlePayerAuthCompletion.bind(this));

                Cardinal.setup(
                    'init',
                    {
                        jwt: config.cardinalJWT
                    }
                );
            },
            handlePayerAuthCompletion: function(data, jwt) {
                // TODO: Runs when validation has completed -- handle response vars and resubmit checkout
                console.log('caught payments.validated', data, jwt);
            },
            getPlaceOrderDeferredObject: function() {
                // Run Cardinal Cruise BIN lookup while the order processes
                if (typeof Cardinal === 'object') {
                    for (var card of this.storedCards()) {
                        if (card.id === this.selectedCard()) {
                            Cardinal.trigger('bin.process', card.cc_bin);
                        }
                    }
                }

                return this._super();
            },
            handleFailedOrder: function(response) {
                // TODO: Check for 475 response and trigger payer auth if so

                var payerAuthMessage = $.mage.__(
                    'The entered card is enrolled in Payer Authentication. Please authenticate before continuing.'
                );
                var error = JSON.parse(response.responseText);
                if (error
                    && typeof error.message !== 'undefined'
                    && typeof Cardinal === 'object'
                    && error.message.indexOf(payerAuthMessage) >= 0) {
                    Cardinal.continue(
                        'cca',
                        {
                            'AcsUrl': 'https://example.com', // TODO: Where the heck does this come from?
                            'Payload': config.cardinalJwt
                        },
                        {
                            'OrderDetails': {
                                'TransactionId': 'abc123' // TODO: Order details
                            }
                        }
                    )
                    return;
                }

                console.log('hit handleFailedOrder', response);
                return this._super();
            }
        });
    }
);
