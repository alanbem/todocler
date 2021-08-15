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

namespace Productivity\Application\Sensor\UsersEvents;

use Productivity\Application\Event as Events;
use Streak\Application;
use Streak\Application\Sensor as Sensors;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @noRector \Rector\Privatization\Rector\Class_\RepeatedLiteralToClassConstantRector
 *
 * @see \Productivity\Application\Sensor\UsersEvents\SensorTest
 * @see \Productivity\Application\Sensor\UsersEvents\Sensor\Factory
 */
final class Sensor implements Application\Sensor
{
    use Sensors\Identification;
    use Sensors\Processing;

    public function processText(string $message) : void
    {
        throw new \InvalidArgumentException('Message not supported.');
    }

    public function processObject(object $message) : void
    {
        throw new \InvalidArgumentException('Message not supported.');
    }

    /**
     * Protobuf would be nice here as strong schema for messages.
     *
     * @see \Users\Application\Projector\Queue\Projector::onUserRegistered()
     */
    public function processMessage(array $message) : void
    {
        if (false === isset($message['name'])) {
            throw new \InvalidArgumentException('[name] field is missing.');
        }
        if (false === isset($message['body'])) {
            throw new \InvalidArgumentException('[body] field is missing.');
        }

        if ('user_registered' === $message['name']) {
            if (false === isset($message['body']['user_id'])) {
                throw new \InvalidArgumentException('[body][user_id] field is missing.');
            }
            if (false === isset($message['body']['email'])) {
                throw new \InvalidArgumentException('[body][email] field is missing.');
            }
            if (false === isset($message['body']['registered_at'])) {
                throw new \InvalidArgumentException('[body][registered_at] field is missing.');
            }

            $event = new Events\UserRegistered(
                $message['body']['user_id'],
                $message['body']['email'],
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s.u P', $message['body']['registered_at']),
            );

            $this->addEvent($event);

            return;
        }

        throw new \InvalidArgumentException('Message not supported.');
    }
}
