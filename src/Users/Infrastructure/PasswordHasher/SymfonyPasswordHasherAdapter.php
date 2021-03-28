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

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Users\Domain\PasswordHasher;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class SymfonyPasswordHasherAdapter implements PasswordHasher
{
    private PasswordEncoderInterface $encoder;

    public function __construct(EncoderFactoryInterface $factory, string $class)
    {
        $this->encoder = $factory->getEncoder($class);
    }

    public function encode(string $password, string $salt) : string
    {
        return $this->encoder->encodePassword($password, $salt);
    }
}
