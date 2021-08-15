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

namespace Productivity\Application\Query;

use Productivity\Application\Projector\Projects;
use Streak\Domain\Event\Listener;
use Streak\Domain\Query;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @see \Productivity\Application\Query\BrowseProjectsTest
 */
final class BrowseProjects implements Query\EventListenerQuery
{
    public function __construct(private ?string $ownerId = null)
    {
    }

    public function ownerId() : ?string
    {
        return $this->ownerId;
    }

    public function listenerId() : Listener\Id
    {
        return new Projects\Projector\Id();
    }
}
