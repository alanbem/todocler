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

namespace Users\Application\Projector\RegisteredUsers\Doctrine\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Very naive implementation of user compatible with Symfony security layer - no salt & single predefined role.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class RegisteredUser implements UserInterface
{
    private string $id;
    private string $username;
    private string $password;
    private string $salt;
    private \DateTimeImmutable $registeredAt;

    public function __construct(string $id, string $username, string $password, string $salt, \DateTimeImmutable $registeredAt)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
        $this->registeredAt = $registeredAt;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getUsername() : string
    {
        return $this->username;
    }

    public function getPassword() : string
    {
        return $this->password;
    }

    public function getRegisteredAt() : \DateTimeImmutable
    {
        return $this->registeredAt;
    }

    public function getRoles() : array
    {
        return [];
    }

    public function getSalt() : string
    {
        return $this->salt;
    }

    public function eraseCredentials() : void
    {
        $this->password = '';
        $this->salt = '';
    }
}
