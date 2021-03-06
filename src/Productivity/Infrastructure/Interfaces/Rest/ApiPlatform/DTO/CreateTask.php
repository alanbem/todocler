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

namespace Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\DTO;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
final class CreateTask
{
    public string $taskId;
    public string $projectId;
    public string $name;
}
