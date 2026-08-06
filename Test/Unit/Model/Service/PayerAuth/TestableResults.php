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

namespace ParadoxLabs\CyberSource\Test\Unit\Model\Service\PayerAuth;

use ParadoxLabs\CyberSource\Model\Service\PayerAuth\Results;

/**
 * Results with the wait recorded instead of taken.
 *
 * The retry schedule spans nearly eight seconds by design (the measured tail on a challenge outcome
 * landing is 4.67s), so a test that actually slept would dominate the suite runtime while proving
 * nothing extra. Recording the delays asserts the schedule itself, which is the part that matters.
 */
class TestableResults extends Results
{
    /**
     * Delays that would have been taken, in microseconds, in order.
     *
     * @var array<int, int>
     */
    public array $pauses = [];

    /**
     * @inheritDoc
     */
    protected function pause(int $microseconds): void
    {
        $this->pauses[] = $microseconds;
    }
}
