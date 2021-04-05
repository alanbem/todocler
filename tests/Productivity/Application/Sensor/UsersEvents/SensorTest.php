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

use Productivity\Application\Event\UserRegistered;
use Productivity\Application\Sensor\UsersEvents\Sensor\Factory;
use Streak\Application\Sensor;
use Streak\Infrastructure\Testing\Sensor\TestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Application\Sensor\UsersEvents\Sensor
 */
final class SensorTest extends TestCase
{
    public function testSensor()
    {
        $this
            ->given(
                ['name' => 'user_registered', 'body' => ['user_id' => '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'email' => 'milton@example.com', 'registered_at' => '2021-04-05 05:00:15.685869 +01:00']]
            )
            ->then(
                new UserRegistered('8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'milton@example.com', \DateTimeImmutable::createFromFormat('Y-m-d H:i:s.u P', '2021-04-05 05:00:15.685869 +01:00'))
            );
    }

    public function testSensorProcessingIncompleteMessage1()
    {
        $this->expectExceptionObject(new \InvalidArgumentException('[name] field is missing.'));

        $this
            ->given(
                ['body' => ['user_id' => '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'email' => 'milton@example.com', 'registered_at' => '2021-04-05 05:00:15.685869 +01:00']]
            )
            ->then();
    }

    public function testSensorProcessingIncompleteMessage2()
    {
        $this->expectExceptionObject(new \InvalidArgumentException('[body] field is missing.'));

        $this
            ->given(
                ['name' => 'user_registered']
            )
            ->then();
    }

    public function testSensorProcessingIncompleteMessage3()
    {
        $this->expectExceptionObject(new \InvalidArgumentException('[body][user_id] field is missing.'));

        $this
            ->given(
                ['name' => 'user_registered', 'body' => ['email' => 'milton@example.com', 'registered_at' => '2021-04-05 05:00:15.685869 +01:00']]
            )
            ->then();
    }

    public function testSensorProcessingIncompleteMessage4()
    {
        $this->expectExceptionObject(new \InvalidArgumentException('[body][email] field is missing.'));

        $this
            ->given(
                ['name' => 'user_registered', 'body' => ['user_id' => '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'registered_at' => '2021-04-05 05:00:15.685869 +01:00']]
            )
            ->then();
    }

    public function testSensorProcessingIncompleteMessage5()
    {
        $this->expectExceptionObject(new \InvalidArgumentException('[body][registered_at] field is missing.'));

        $this
            ->given(
                ['name' => 'user_registered', 'body' => ['user_id' => '8e5ebf2b-f78c-430d-b15f-0f3e710b284b', 'email' => 'milton@example.com']]
            )
            ->then();
    }

    public function testSensorProcessingWrongMessage1()
    {
        $this->expectExceptionObject(new \InvalidArgumentException('Message not supported.'));

        $this
            ->given(
                ['name' => 'wrong_message', 'body' => 'body']
            )
            ->then();
    }

    public function testSensorProcessingText()
    {
        $this->expectExceptionObject(new \InvalidArgumentException('Message not supported.'));

        $this
            ->given(
                'Lorem ipsum...'
            )
            ->then();
    }

    public function testSensorProcessingObject()
    {
        $this->expectExceptionObject(new \InvalidArgumentException('Message not supported.'));

        $this
            ->given(
                new \stdClass()
            )
            ->then();
    }

    public function createFactory() : Sensor\Factory
    {
        return new Factory();
    }
}
