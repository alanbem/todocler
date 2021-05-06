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

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Users\Domain\PasswordHasher;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Users\Infrastructure\Domain\PasswordHasher\SymfonyPasswordHasherAdapterTest
 */
final class SymfonyPasswordHasherAdapter implements PasswordHasher
{
    private PasswordHasherInterface $hasher;

    public function __construct(PasswordHasherFactoryInterface $factory, string $class)
    {
        $this->hasher = $factory->getPasswordHasher($class);
    }

    public function hash(string $password) : string
    {
        return $this->hasher->hash($password);
    }
}
