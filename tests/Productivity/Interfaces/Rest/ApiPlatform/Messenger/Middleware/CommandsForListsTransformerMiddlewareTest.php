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

namespace Productivity\Interfaces\Rest\ApiPlatform\Messenger\Middleware;

use ApiPlatform\Core\Bridge\Symfony\Messenger\ContextStamp;
use PHPUnit\Framework\TestCase;
use Productivity\Application\Command as Commands;
use Productivity\Application\Projector\Lists\Doctrine\Entity as Entitites;
use Productivity\Interfaces\Rest\ApiPlatform\DTO as DTOs;
use Productivity\UsersFacade;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Interfaces\Rest\ApiPlatform\Messenger\Middleware\CommandsForListsTransformerMiddleware
 */
class CommandsForListsTransformerMiddlewareTest extends TestCase
{
    private Security $security;
    private UserInterface $user;
    private UsersFacade $facade;
    private StackInterface $stack;
    private MiddlewareInterface $next;

    protected function setUp() : void
    {
        $this->security = $this->createMock(Security::class);
        $this->user = $this->createMock(UserInterface::class);
        $this->facade = $this->createMock(UsersFacade::class);
        $this->stack = $this->createMock(StackInterface::class);
        $this->next = $this->createMock(MiddlewareInterface::class);

        $this->stack
            ->expects($this->once())
            ->method('next')
            ->willReturn($this->next);

        $this->next
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(Envelope::class), $this->stack)
            ->willReturnCallback(fn (Envelope $envelope, StackInterface $stack) => $envelope);
    }

    public function testMiddlewareForCreateList() : void
    {
        $middleware = new CommandsForListsTransformerMiddleware($this->security, $this->facade);

        $message = new DTOs\CreateList();
        $message->listId = '62fafb74-f550-4780-a527-37cb0b1e08ae';
        $message->name = 'name';
        $context = ['previous_data' => new Entitites\Checklist('62fafb74-f550-4780-a527-37cb0b1e08ae', 'name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable())];
        $envelope = Envelope::wrap($message, [new ContextStamp($context)]);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects($this->once())
            ->method('getUsername')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects($this->once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']);

        $command = new Commands\CreateList('62fafb74-f550-4780-a527-37cb0b1e08ae', 'name', '6b244a62-0e1a-45ec-ac01-eb0f805432d9');
        $expected = Envelope::wrap($command);

        $actual = $middleware->handle($envelope, $this->stack);

        $this->assertEquals($expected, $actual);
    }

    public function testMiddlewareForRenameList() : void
    {
        $middleware = new CommandsForListsTransformerMiddleware($this->security, $this->facade);

        $message = new DTOs\RenameList();
        $message->name = 'new name';
        $context = ['previous_data' => new Entitites\Checklist('62fafb74-f550-4780-a527-37cb0b1e08ae', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable())];
        $envelope = Envelope::wrap($message, [new ContextStamp($context)]);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects($this->once())
            ->method('getUsername')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects($this->once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']);

        $command = new Commands\RenameList('62fafb74-f550-4780-a527-37cb0b1e08ae', 'new name', '6b244a62-0e1a-45ec-ac01-eb0f805432d9');
        $expected = Envelope::wrap($command);

        $actual = $middleware->handle($envelope, $this->stack);

        $this->assertEquals($expected, $actual);
    }

    public function testMiddlewareForCreateListWhenUserNotAuthenticated() : void
    {
        $middleware = new CommandsForListsTransformerMiddleware($this->security, $this->facade);

        $message = new DTOs\CreateList();
        $message->listId = '62fafb74-f550-4780-a527-37cb0b1e08ae';
        $message->name = 'name';
        $context = ['previous_data' => new Entitites\Checklist('62fafb74-f550-4780-a527-37cb0b1e08ae', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable())];
        $envelope = Envelope::wrap($message, [new ContextStamp($context)]);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->user
            ->expects($this->never())
            ->method($this->anything());

        $this->facade
            ->expects($this->never())
            ->method($this->anything());

        $actual = $middleware->handle($envelope, $this->stack);

        $this->assertSame($envelope, $actual);
    }

    public function testMiddlewareForRenameListWhenUserNotAuthenticated() : void
    {
        $middleware = new CommandsForListsTransformerMiddleware($this->security, $this->facade);

        $message = new DTOs\RenameList();
        $message->name = 'new name';
        $context = ['previous_data' => new Entitites\Checklist('62fafb74-f550-4780-a527-37cb0b1e08ae', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable())];
        $envelope = Envelope::wrap($message, [new ContextStamp($context)]);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->user
            ->expects($this->never())
            ->method($this->anything());

        $this->facade
            ->expects($this->never())
            ->method($this->anything());

        $actual = $middleware->handle($envelope, $this->stack);

        $this->assertSame($envelope, $actual);
    }

    public function testMiddlewareForCreateListWhenUserNotRegistered() : void
    {
        $middleware = new CommandsForListsTransformerMiddleware($this->security, $this->facade);

        $message = new DTOs\CreateList();
        $message->listId = '62fafb74-f550-4780-a527-37cb0b1e08ae';
        $message->name = 'name';
        $context = ['previous_data' => new Entitites\Checklist('62fafb74-f550-4780-a527-37cb0b1e08ae', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable())];
        $envelope = Envelope::wrap($message, [new ContextStamp($context)]);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects($this->once())
            ->method('getUsername')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects($this->once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn(null);

        $actual = $middleware->handle($envelope, $this->stack);

        $this->assertSame($envelope, $actual);
    }

    public function testMiddlewareForRenameListWhenUserNotRegistered() : void
    {
        $middleware = new CommandsForListsTransformerMiddleware($this->security, $this->facade);

        $message = new DTOs\RenameList();
        $message->name = 'new name';
        $context = ['previous_data' => new Entitites\Checklist('62fafb74-f550-4780-a527-37cb0b1e08ae', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable())];
        $envelope = Envelope::wrap($message, [new ContextStamp($context)]);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects($this->once())
            ->method('getUsername')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects($this->once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn(null);

        $actual = $middleware->handle($envelope, $this->stack);

        $this->assertSame($envelope, $actual);
    }

    public function testMiddlewareForCreateListWhenNoStamp() : void
    {
        $middleware = new CommandsForListsTransformerMiddleware($this->security, $this->facade);

        $message = new DTOs\CreateList();
        $message->listId = '62fafb74-f550-4780-a527-37cb0b1e08ae';
        $message->name = 'name';
        $envelope = Envelope::wrap($message, []);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects($this->once())
            ->method('getUsername')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects($this->once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']);

        $command = new Commands\CreateList('62fafb74-f550-4780-a527-37cb0b1e08ae', 'name', '6b244a62-0e1a-45ec-ac01-eb0f805432d9');
        $expected = Envelope::wrap($command);

        $actual = $middleware->handle($envelope, $this->stack);

        $this->assertEquals($expected, $actual);
    }

    public function testMiddlewareForRenameListWhenNoStamp() : void
    {
        $middleware = new CommandsForListsTransformerMiddleware($this->security, $this->facade);

        $message = new DTOs\RenameList();
        $message->name = 'new name';
        $envelope = Envelope::wrap($message, []);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects($this->once())
            ->method('getUsername')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects($this->once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']);

        $actual = $middleware->handle($envelope, $this->stack);

        $this->assertSame($envelope, $actual);
    }

    public function testMiddlewareForCreateListWhenWrongContext() : void
    {
        $middleware = new CommandsForListsTransformerMiddleware($this->security, $this->facade);

        $message = new DTOs\CreateList();
        $message->listId = '62fafb74-f550-4780-a527-37cb0b1e08ae';
        $message->name = 'name';
        $context = [];
        $envelope = Envelope::wrap($message, [new ContextStamp($context)]);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects($this->once())
            ->method('getUsername')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects($this->once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']);

        $command = new Commands\CreateList('62fafb74-f550-4780-a527-37cb0b1e08ae', 'name', '6b244a62-0e1a-45ec-ac01-eb0f805432d9');
        $expected = Envelope::wrap($command);

        $actual = $middleware->handle($envelope, $this->stack);

        $this->assertEquals($expected, $actual);
    }

    public function testMiddlewareForRenameListWhenWrongContext() : void
    {
        $middleware = new CommandsForListsTransformerMiddleware($this->security, $this->facade);

        $message = new DTOs\RenameList();
        $message->name = 'new name';
        $context = [];
        $envelope = Envelope::wrap($message, [new ContextStamp($context)]);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects($this->once())
            ->method('getUsername')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects($this->once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']);

        $actual = $middleware->handle($envelope, $this->stack);

        $this->assertSame($envelope, $actual);
    }

    public function testMiddlewareForCreateListWhenWrongResource() : void
    {
        $middleware = new CommandsForListsTransformerMiddleware($this->security, $this->facade);

        $message = new DTOs\CreateList();
        $message->listId = '62fafb74-f550-4780-a527-37cb0b1e08ae';
        $message->name = 'name';
        $context = ['previous_data' => new Entitites\Checklist('62fafb74-f550-4780-a527-37cb0b1e08ae', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable())];
        $envelope = Envelope::wrap($message, [new ContextStamp($context)]);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects($this->once())
            ->method('getUsername')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects($this->once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']);

        $command = new Commands\CreateList('62fafb74-f550-4780-a527-37cb0b1e08ae', 'name', '6b244a62-0e1a-45ec-ac01-eb0f805432d9');
        $expected = Envelope::wrap($command);

        $actual = $middleware->handle($envelope, $this->stack);

        $this->assertEquals($expected, $actual);
    }

    public function testMiddlewareForRenameListWhenWrongResource() : void
    {
        $middleware = new CommandsForListsTransformerMiddleware($this->security, $this->facade);

        $message = new DTOs\RenameList();
        $message->name = 'new name';
        $context = ['previous_data' => new Entitites\Task(new Entitites\Checklist('62fafb74-f550-4780-a527-37cb0b1e08ae', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable()), '8b89a300-a95b-40af-b235-87ddf9e47309', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable())];
        $envelope = Envelope::wrap($message, [new ContextStamp($context)]);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects($this->once())
            ->method('getUsername')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects($this->once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']);

        $actual = $middleware->handle($envelope, $this->stack);

        $this->assertSame($envelope, $actual);
    }

    public function testMiddlewareWhenWrongMessage() : void
    {
        $middleware = new CommandsForListsTransformerMiddleware($this->security, $this->facade);

        $message = new DTOs\CompleteTask();
        $context = ['previous_data' => new Entitites\Checklist('62fafb74-f550-4780-a527-37cb0b1e08ae', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable())];
        $envelope = Envelope::wrap($message, [new ContextStamp($context)]);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->user);

        $this->user
            ->expects($this->once())
            ->method('getUsername')
            ->willReturn('john.doe@example.com');

        $this->facade
            ->expects($this->once())
            ->method('findRegisteredUser')
            ->with('john.doe@example.com')
            ->willReturn((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']);

        $actual = $middleware->handle($envelope, $this->stack);

        $this->assertSame($envelope, $actual);
    }
}
