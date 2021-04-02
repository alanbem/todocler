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

namespace Users\Infrastructure\SaltGenerator;

use PHPUnit\Framework\TestCase;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Infrastructure\SaltGenerator\FixedSaltGenerator
 */
final class FixedSaltGeneratorTest extends TestCase
{
    public function testGenerator() : void
    {
        $generator = new FixedSaltGenerator('fixed-salt-1');
        $salt = $generator->generate();

        $this->assertSame('fixed-salt-1', $salt);

        $generator = new FixedSaltGenerator('fixed-salt-2');
        $salt = $generator->generate();

        $this->assertSame('fixed-salt-2', $salt);
    }
}
