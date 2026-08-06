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
     * How long to wait before each re-read of an outcome that has not landed yet, in microseconds.
     *
     * The ACS reports the challenge outcome to CyberSource out of band (RReq), so the results call
     * can arrive first and read a reply that carries no paresStatus at all. Measured over 22 sandbox
     * challenges on 2026-08-06: the outcome landed anywhere from 173 ms to 4670 ms after the browser
     * leg ended — mostly under 300 ms, with a long tail (5 of 22 over 1.5 s). An early 8-sample read
     * topped out at 1043 ms and is what a too-tight first fix was sized against; do not re-tighten
     * this on a small sample.
     *
     * Escalating rather than fixed: the common case still resolves in one extra call a quarter of a
     * second later, while the tail gets ~7.75 s of cover for seven calls total instead of thirty.
     * The wait only happens on a challenge the shopper has just finished, where some delay reads as
     * processing — and losing the race costs them the liability shift, or the order under
     * require-3DS, which is far worse than waiting.
     */
    private const OUTCOME_RETRY_DELAYS_US = [250000, 500000, 1000000, 1500000, 2000000, 2500000];

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

        foreach (self::OUTCOME_RETRY_DELAYS_US as $delay) {
            if ($this->hasOutcome($reply)) {
                break;
            }

            $this->pause($delay);

            $reply = $this->rest->post(self::RESULTS_PATH, $body);
        }

        return $this->classifier->classify($reply);
    }

    /**
     * Wait between re-reads. Seam so tests can exercise the retry schedule without sleeping.
     *
     * @param int $microseconds
     * @return void
     */
    protected function pause(int $microseconds): void
    {
        usleep($microseconds);
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
