<?php

/**
 * This file is part of the todocler package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Shared\Infrastructure\Behat;

use Behat\Behat\Context\Context;
use Streak\Domain\Event\Subscription;
use Streak\Domain\EventStore;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @codeCoverageIgnore
 */
final class StreakContext implements Context
{
    private Subscription\Repository $subscriptions;
    private EventStore $store;

    public function __construct(Subscription\Repository $subscriptions, EventStore $store)
    {
        $this->subscriptions = $subscriptions;
        $this->store = $store;
    }

    /**
     * @BeforeScenario
     */
    public function restartSubscriptions() : void
    {
        $filter = Subscription\Repository\Filter::nothing()->ignoreCompletedSubscriptions();
        $subscriptions = $this->subscriptions->all($filter);

        foreach ($subscriptions as $subscription) {
            $subscription->restart();
        }
    }

    /**
     * @AfterStep
     */
    public function runSubscriptions() : void
    {
        do {
            $filter = Subscription\Repository\Filter::nothing()->ignoreCompletedSubscriptions();
            $subscriptions = $this->subscriptions->all($filter);
            $processed = [];
            foreach ($subscriptions as $subscription) {
                foreach ($subscription->subscribeTo($this->store) as $event) {
                    $processed[] = $event;
                }
            }
        } while (0 !== \count($processed)); // process subscriptions until no more events are left
    }
}
