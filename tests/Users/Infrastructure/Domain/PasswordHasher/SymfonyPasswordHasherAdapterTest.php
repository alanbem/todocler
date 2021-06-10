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

namespace Users\Infrastructure\Domain\PasswordHasher;

use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Users\Infrastructure\Domain\PasswordHasher\SymfonyPasswordHasherAdapter
 */
final class SymfonyPasswordHasherAdapterTest extends TestCase
{
    private PasswordHasherFactoryInterface $factory;
    private PasswordHasherInterface $hasher;

    protected function setUp() : void
    {
        $this->factory = $this->createMock(PasswordHasherFactoryInterface::class);
        $this->hasher = $this->createMock(PasswordHasherInterface::class);
    }

    public function testAdapter() : void
    {
        $this->factory
            ->expects(self::once())
            ->method('getPasswordHasher')
            ->with('FQCN')
            ->willReturn($this->hasher)
        ;

        $adapter = new SymfonyPasswordHasherAdapter($this->factory, 'FQCN');

        $this->hasher
            ->expects(self::exactly(2))
            ->method('hash')
            ->withConsecutive(
                ['password-1'],
                ['password-2'],
            )
            ->willReturnOnConsecutiveCalls(
                'hash-1',
                'hash-2',
            );

        $hash = $adapter->hash('password-1');
        self::assertSame('hash-1', $hash);

        $hash = $adapter->hash('password-2');
        self::assertSame('hash-2', $hash);
    }
}
