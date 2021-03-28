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

use Users\Domain\SaltGenerator;
use Webmozart\Assert\Assert;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Users\Infrastructure\SaltGenerator\FixedSaltGeneratorTest
 */
class FixedSaltGenerator implements SaltGenerator
{
    private string $salt;

    public function __construct(string $salt)
    {
        Assert::notEmpty($salt, '$salt can not be empty');

        $this->salt = $salt;
    }

    public function generate() : string
    {
        return $this->salt;
    }
}
