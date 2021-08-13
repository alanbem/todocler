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
 *
 * @see \Users\Application\Projector\RegisteredUsers\Doctrine\Entity\RegisteredUserTest
 *
 * @noRector \Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector
 */
class RegisteredUser implements UserInterface
{
    public function __construct(private string $id, private string $email, private string $password, private \DateTimeImmutable $registeredAt)
    {
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getEmail() : string
    {
        return $this->email;
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
        return '';
    }

    public function getUserIdentifier() : string
    {
        return $this->email;
    }

    public function getUsername() : string
    {
        return $this->email;
    }

    public function eraseCredentials() : void
    {
        $this->password = '';
    }
}
