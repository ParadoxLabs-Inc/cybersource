<?php declare(strict_types=1);
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
 *
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\CyberSource\Model\Service\PayerAuth;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\RuntimeException;
use ParadoxLabs\CyberSource\Model\Config\Config;
use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Request\ResultsRequest;
use ParadoxLabs\CyberSource\Model\Service\Rest;

/**
 * POST /risk/v1/authentication-results — finalize a challenge (step-up) authentication.
 *
 * Thin transport wrapper: same reply shape as /risk/v1/authentications, so the same classifier
 * rules apply to the finalized outcome.
 */
class Results
{
    /**
     * Authentication results REST endpoint path.
     */
    public const RESULTS_PATH = '/risk/v1/authentication-results';

    /**
     * How many times to re-read an outcome that has not landed yet, and how long to wait between.
     *
     * The ACS reports the challenge outcome to CyberSource out of band (RReq), so the results call
     * can arrive first and read a reply that carries no paresStatus at all. Measured 2026-08-06
     * over 8 sandbox challenges: the outcome landed 173-1043 ms after the browser leg ended. Five
     * attempts 250 ms apart cover ~1.25 s of that window on top of the client round-trip that has
     * already elapsed, without holding the request open long enough to matter to a shopper.
     */
    private const OUTCOME_MAX_ATTEMPTS = 5;
    private const OUTCOME_RETRY_DELAY_US = 250000;

    /**
     * Results constructor.
     *
     * @param Config $config
     * @param Rest $rest
     * @param ResultClassifier $classifier
     */
    public function __construct(
        private readonly Config $config,
        private readonly Rest $rest,
        private readonly ResultClassifier $classifier
    ) {
    }

    /**
     * Fetch and classify the final authentication result for a challenged transaction.
     *
     * @param ResultsRequest $request
     * @param int|null $storeId Config/credential scope; null for the assumed scope.
     * @return AuthenticationResult
     * @throws InputException On an invalid request DTO (missing transaction id).
     * @throws RuntimeException On any non-2xx response.
     */
    public function execute(ResultsRequest $request, ?int $storeId = null): AuthenticationResult
    {
        $this->config->setStoreId($storeId);
        $this->rest->setStoreId($storeId);

        $body = $request->toArray();
        $reply = $this->rest->post(self::RESULTS_PATH, $body);

        for ($attempt = 1; $attempt < self::OUTCOME_MAX_ATTEMPTS && !$this->hasOutcome($reply); $attempt++) {
            usleep(self::OUTCOME_RETRY_DELAY_US);

            $reply = $this->rest->post(self::RESULTS_PATH, $body);
        }

        return $this->classifier->classify($reply);
    }

    /**
     * Whether the reply carries a settled authentication outcome.
     *
     * A challenge that has not been reported to CyberSource yet answers AUTHENTICATION_SUCCESSFUL
     * with NO paresStatus and no CAVV — the same shape a genuinely unavailable authentication
     * produces, which ResultClassifier can only read as Verdict::UNAVAILABLE. Since this service
     * only ever finalizes a transaction that already reached PENDING_AUTHENTICATION, a missing
     * paresStatus here means "not landed yet", not "no answer": the DS has spoken by definition.
     * Any paresStatus at all — including U — is terminal and must NOT be retried.
     *
     * @param array<string, mixed> $reply
     * @return bool
     */
    private function hasOutcome(array $reply): bool
    {
        $paresStatus = $reply['consumerAuthenticationInformation']['paresStatus'] ?? null;

        return is_scalar($paresStatus) && (string)$paresStatus !== '';
    }
}
