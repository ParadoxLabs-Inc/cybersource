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

/**
 * Payer Authentication outcome, as classified from a /risk/v1/authentications(-results) reply.
 *
 * The top-level reply `status` is NOT a verdict: unavailable/error/timeout/bypass shapes all come
 * back as AUTHENTICATION_SUCCESSFUL (see PAYER-AUTH-PLAN.md, outcome matrix 2.1-2.9). Only
 * ResultClassifier may produce these values.
 *
 * - AUTHENTICATED / ATTEMPTED: liability shift; attach the pass-through block to the payment.
 * - UNAVAILABLE: place WITHOUT pass-through; log the outcome.
 * - FAILED: hard stop, no order attempt.
 * - CHALLENGE: surface acsUrl/pareq to the client for the step-up.
 */
enum Verdict: string
{
    case AUTHENTICATED = 'authenticated';
    case ATTEMPTED = 'attempted';
    case UNAVAILABLE = 'unavailable';
    case FAILED = 'failed';
    case CHALLENGE = 'challenge';

    /**
     * Whether this verdict carries a liability shift (i.e. the pass-through block may be attached).
     *
     * @return bool
     */
    public function hasLiabilityShift(): bool
    {
        return $this === self::AUTHENTICATED || $this === self::ATTEMPTED;
    }
}
