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

namespace Productivity\Domain\Exception;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class UserNotPermitted extends \RuntimeException
{
    private string $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;

        $message = sprintf('User "%s" not permitted.', $this->userId);

        parent::__construct($message);
    }

    public function userId() : string
    {
        return $this->userId;
    }
}
