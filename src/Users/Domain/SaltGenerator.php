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

namespace Users\Domain;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
interface SaltGenerator
{
    public function generate() : string;
}