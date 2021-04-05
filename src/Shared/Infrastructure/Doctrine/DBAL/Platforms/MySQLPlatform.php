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

namespace Shared\Infrastructure\Doctrine\DBAL\Platforms;

use Doctrine\DBAL\Platforms\MySQL57Platform;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @codeCoverageIgnore
 */
final class MySQLPlatform extends MySQL57Platform
{
    public function getDateTimeTzFormatString()
    {
        return 'Y-m-d H:i:s.u';
    }
}
