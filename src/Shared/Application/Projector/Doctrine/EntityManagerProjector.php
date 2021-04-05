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

namespace Shared\Application\Projector\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Streak\Domain\Event;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
abstract class EntityManagerProjector implements Event\Listener, Event\Listener\Resettable, Event\Filterer
{
    use Event\Listener\Identifying;
    use Event\Listener\Listening;
    use Event\Listener\Filtering;

    protected EntityManagerInterface $manager;

    public function __construct(Event\Listener\Id $id, EntityManagerInterface $manager)
    {
        $this->identifyBy($id);

        $this->manager = $manager;
    }

    public function reset() : void
    {
        $this->manager->beginTransaction();

        $tool = new SchemaTool($this->manager);
        $meta = $this->manager->getMetadataFactory()->getAllMetadata();

        try {
            $tool->dropSchema($meta);
            $tool->createSchema($meta);
            // @codeCoverageIgnoreStart
        } catch (ToolsException $e) {
            $this->manager->rollback();

            throw $e;
        }
        // @codeCoverageIgnoreStop

        $this->manager->commit();
    }

    protected function preEvent(Event $event) : void
    {
        $this->manager->clear();
        $this->manager->beginTransaction();
    }

    protected function postEvent(Event $event) : void
    {
        $this->manager->flush();
        $this->manager->commit();
    }

    protected function onException(\Throwable $exception) : void
    {
        $this->manager->rollBack();
    }
}
