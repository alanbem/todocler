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

namespace Users\Infrastructure\Application\Queue;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Wire\AMQPTable;
use Users\Application\Projector\Queue\Projector\Queue;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Users\Infrastructure\Application\Queue\RabbitMQQueueTest
 */
final class RabbitMQQueue implements Queue
{
    public function __construct(private ProducerInterface $producer, private string $module)
    {
    }

    /**
     * Protobuf would be nice here as projecting to downstream consumers requires some kind of strong schema.
     *
     * @see \Productivity\Application\Sensor\UsersEvents\Sensor::processMessage()
     */
    public function send(string $id, string $name, array $body) : void
    {
        // @TODO: validate name
        $message = [
            'name' => $name,
            'body' => $body,
        ];
        $message = json_encode($message);

        $headers = ['x-deduplication-header' => $id];
        $headers = new AMQPTable($headers);

        $this->producer->publish($message, $this->module.'.'.$name, ['content_type' => 'application/json', 'application_headers' => $headers]);
    }
}
