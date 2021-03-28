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

namespace Users\Infrastructure\PasswordHasher;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Infrastructure\PasswordHasher\SymfonyPasswordHasherAdapter
 */
class SymfonyPasswordHasherAdapterTest extends TestCase
{
    private EncoderFactoryInterface $factory;
    private PasswordEncoderInterface $encoder;

    protected function setUp() : void
    {
        $this->factory = $this->createMock(EncoderFactoryInterface::class);
        $this->encoder = $this->createMock(PasswordEncoderInterface::class);
    }

    public function testAdapter() : void
    {
        $this->factory
            ->expects($this->once())
            ->method('getEncoder')
            ->with('FQCN')
            ->willReturn($this->encoder)
        ;

        $adapter = new SymfonyPasswordHasherAdapter($this->factory, 'FQCN');

        $this->encoder
            ->expects($this->exactly(2))
            ->method('encodePassword')
            ->withConsecutive(
                ['password-1', 'salt-1'],
                ['password-2', 'salt-2'],
            )
            ->willReturnOnConsecutiveCalls(
                'hash-1',
                'hash-2',
            );

        $hash = $adapter->encode('password-1', 'salt-1');
        $this->assertSame('hash-1', $hash);

        $hash = $adapter->encode('password-2', 'salt-2');
        $this->assertSame('hash-2', $hash);
    }
}
