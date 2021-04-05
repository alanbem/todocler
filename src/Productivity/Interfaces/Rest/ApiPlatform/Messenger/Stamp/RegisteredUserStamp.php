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

namespace Productivity\Interfaces\Rest\ApiPlatform\Messenger\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Interfaces\Rest\ApiPlatform\Messenger\Stamp\RegisteredUserStampTest
 */
final class RegisteredUserStamp implements StampInterface
{
    private object $user;

    public function __construct(object $user)
    {
        $this->user = $user;
    }

    public function user() : object
    {
        return $this->user;
    }
}
