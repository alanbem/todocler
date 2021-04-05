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

namespace Productivity\Application\Sensor\UsersEvents\Sensor;

use Productivity\Application\Sensor\UsersEvents;
use Streak\Application\Sensor;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Application\Sensor\UsersEvents\Sensor\FactoryTest
 */
final class Factory implements Sensor\Factory
{
    public function create() : Sensor
    {
        return new UsersEvents\Sensor(new UsersEvents\Sensor\Id());
    }
}
