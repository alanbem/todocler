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

use PHPUnit\Framework\TestCase;
use Productivity\Application\Sensor\UsersEvents\Sensor;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Application\Sensor\UsersEvents\Sensor\Factory
 */
final class FactoryTest extends TestCase
{
    public function testFactory() : void
    {
        $factory = new Sensor\Factory();

        $aggregate = $factory->create();

        self::assertEquals(new Sensor(new Sensor\Id()), $aggregate);
    }
}
