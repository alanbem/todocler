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
use PHPUnit\Framework\TestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Infrastructure\Application\Queue\RabbitMQQueue
 */
final class RabbitMQQueueTest extends TestCase
{
    private ProducerInterface $producer;

    protected function setUp() : void
    {
        $this->producer = $this->createMock(ProducerInterface::class);
    }

    public function testQueue() : void
    {
        $this->producer
            ->expects(self::once())
            ->method('publish')
            ->with('{"name":"name_value","body":["3489eoywhbyfbcueyrbu"]}', 'module_name.name_value', ['content_type' => 'application/json', 'application_headers' => new AMQPTable(['x-deduplication-header' => 'id-3424'])]);

        $queue = new RabbitMQQueue($this->producer, 'module_name');
        $queue->send('id-3424', 'name_value', ['3489eoywhbyfbcueyrbu']);
    }
}
