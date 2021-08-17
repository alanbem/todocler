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

use PHPUnit\Framework\TestCase;
use Productivity\Application\Projector\Projects;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Application\Query\BrowseProjects
 */
final class BrowseProjectsTest extends TestCase
{
    public function testQuery() : void
    {
        $query = new BrowseProjects(null);

        self::assertNull($query->ownerId());
        self::assertEquals($query->listenerId(), new Projects\Projector\Id());

        $query = new BrowseProjects('user-1');

        self::assertSame('user-1', $query->ownerId());
        self::assertEquals($query->listenerId(), new Projects\Projector\Id());
    }
}
