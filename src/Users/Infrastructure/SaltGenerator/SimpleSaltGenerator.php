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

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @codeCoverageIgnore
 */
class SimpleSaltGenerator implements SaltGenerator
{
    public function generate() : string
    {
        return uniqid((string) mt_rand(), true);
    }
}
