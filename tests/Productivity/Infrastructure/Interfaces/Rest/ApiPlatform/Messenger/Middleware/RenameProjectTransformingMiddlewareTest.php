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

namespace Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Messenger\Middleware;

use ApiPlatform\Core\Bridge\Symfony\Messenger\ContextStamp;
use PHPUnit\Framework\TestCase;
use Productivity\Application\Projector\Projects\Doctrine\Entity as Entities;
use Productivity\Domain\Command as Commands;
use Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\DTO as DTOs;
use Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Messenger\Stamp\RegisteredUserStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @covers \Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Messenger\Middleware\RenameProjectTransformingMiddleware
 * @covers \Productivity\Infrastructure\Interfaces\Rest\ApiPlatform\Messenger\Middleware\TransformingMiddleware
 */
final class RenameProjectTransformingMiddlewareTest extends TestCase
{
    private StackInterface $stack;
    private MiddlewareInterface $next;

    protected function setUp() : void
    {
        $this->stack = $this->createMock(StackInterface::class);
        $this->next = $this->createMock(MiddlewareInterface::class);

        $this->stack
            ->expects(self::once())
            ->method('next')
            ->willReturn($this->next);

        $this->next
            ->expects(self::once())
            ->method('handle')
            ->with(self::isInstanceOf(Envelope::class), $this->stack)
            ->willReturnCallback(fn (Envelope $envelope, StackInterface $stack) => $envelope);
    }

    public function testMiddleware() : void
    {
        $middleware = new RenameProjectTransformingMiddleware();

        $message = new DTOs\RenameProject();
        $message->name = 'name';
        $context = ['previous_data' => new Entities\Project('62fafb74-f550-4780-a527-37cb0b1e08ae', 'name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable())];

        $envelope = Envelope::wrap($message);
        $envelope = $envelope->with(new RegisteredUserStamp((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']));
        $envelope = $envelope->with(new ContextStamp($context));

        $command = new Commands\RenameProject('62fafb74-f550-4780-a527-37cb0b1e08ae', 'name', '6b244a62-0e1a-45ec-ac01-eb0f805432d9');
        $expected = Envelope::wrap($command);

        $actual = $middleware->handle($envelope, $this->stack);

        self::assertEquals($expected, $actual);
    }

    public function testMiddlewareWithWrongMessage() : void
    {
        $middleware = new RenameProjectTransformingMiddleware();

        $message = new \stdClass();
        $context = ['previous_data' => new Entities\Project('62fafb74-f550-4780-a527-37cb0b1e08ae', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable())];

        $envelope = Envelope::wrap($message);
        $envelope = $envelope->with(new RegisteredUserStamp((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']));
        $envelope = $envelope->with(new ContextStamp($context));

        $actual = $middleware->handle($envelope, $this->stack);

        self::assertSame($envelope, $actual);
    }

    public function testMiddlewareWithoutContext() : void
    {
        $middleware = new RenameProjectTransformingMiddleware();

        $message = new DTOs\RenameProject();
        $message->name = 'name';

        $envelope = Envelope::wrap($message);
        $envelope = $envelope->with(new RegisteredUserStamp((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']));

        $actual = $middleware->handle($envelope, $this->stack);

        self::assertSame($envelope, $actual);
    }

    public function testMiddlewareWithWrongContext() : void
    {
        $middleware = new RenameProjectTransformingMiddleware();

        $message = new DTOs\RenameProject();
        $message->name = 'name';
        $context = [];

        $envelope = Envelope::wrap($message);
        $envelope = $envelope->with(new RegisteredUserStamp((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']));
        $envelope = $envelope->with(new ContextStamp($context));

        $actual = $middleware->handle($envelope, $this->stack);

        self::assertSame($envelope, $actual);
    }

    public function testMiddlewareWithWrongResource() : void
    {
        $middleware = new RenameProjectTransformingMiddleware();

        $message = new DTOs\RenameProject();
        $message->name = 'name';
        $context = ['previous_data' => new Entities\Task(new Entities\Project('62fafb74-f550-4780-a527-37cb0b1e08ae', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable()), '8b89a300-a95b-40af-b235-87ddf9e47309', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable())];

        $envelope = Envelope::wrap($message);
        $envelope = $envelope->with(new RegisteredUserStamp((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']));
        $envelope = $envelope->with(new ContextStamp($context));

        $actual = $middleware->handle($envelope, $this->stack);

        self::assertSame($envelope, $actual);
    }

    public function testMiddlewareWithoutResource() : void
    {
        $middleware = new RenameProjectTransformingMiddleware();

        $message = new DTOs\RenameProject();
        $message->name = 'name';
        $context = ['previous_data' => ''];

        $envelope = Envelope::wrap($message);
        $envelope = $envelope->with(new RegisteredUserStamp((object) ['id' => '6b244a62-0e1a-45ec-ac01-eb0f805432d9', 'john.doe@example.com']));
        $envelope = $envelope->with(new ContextStamp($context));

        $actual = $middleware->handle($envelope, $this->stack);

        self::assertSame($envelope, $actual);
    }

    public function testMiddlewareWithoutRegisteredUser() : void
    {
        $middleware = new RenameProjectTransformingMiddleware();

        $message = new DTOs\RenameProject();
        $message->name = 'name';
        $context = ['previous_data' => new Entities\Project('62fafb74-f550-4780-a527-37cb0b1e08ae', 'old name', '1a9d8b27-5896-4d50-b540-0d92b1708747', new \DateTimeImmutable(), new \DateTimeImmutable())];

        $envelope = Envelope::wrap($message);
        $envelope = $envelope->with(new ContextStamp($context));

        $actual = $middleware->handle($envelope, $this->stack);

        self::assertSame($envelope, $actual);
    }
}
