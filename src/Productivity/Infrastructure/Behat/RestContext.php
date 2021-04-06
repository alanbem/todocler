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

namespace Productivity\Infrastructure\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;

/**
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * @codeCoverageIgnore
 */
final class RestContext implements Context
{
    /**
     * @Given I am signed in as :email with password :password
     */
    public function iAmSignedInWithEmailAndPassword(string $email, string $password) : void
    {
        throw new PendingException();
    }

    /**
     * @When I create new list :name
     */
    public function iCreateNewList(string $name) : void
    {
        throw new PendingException();
    }

    /**
     * @Then I can see list named :name
     */
    public function iCanSeeListNamed(string $name) : void
    {
        throw new PendingException();
    }

    /**
     * @Then list :name has no tasks yet
     */
    public function listHasNoTasksYet(string $name) : void
    {
        throw new PendingException();
    }

    /**
     * @When I create new task :task under :list list
     */
    public function iCreateNewTaskUnderList(string $task, string $list) : void
    {
        throw new PendingException();
    }

    /**
     * @Then list :list has task :task assigned
     */
    public function listHasTaskAssigned(string $list, string $task) : void
    {
        throw new PendingException();
    }

    /**
     * @Then task :task under list :list is not yet completed
     */
    public function taskUnderListIsNotYetCompleted(string $task, string $list) : void
    {
        throw new PendingException();
    }

    /**
     * @When I mark task :task under list :list as completed
     */
    public function iMarkTaskUnderListAsCompleted(string $task, string $list) : void
    {
        throw new PendingException();
    }

    /**
     * @When task :task under list :list is completed
     */
    public function taskUnderListIsCompleted(string $task, string $list) : void
    {
        throw new PendingException();
    }

    /**
     * @Given there are users registered in system:
     */
    public function thereAreUsersRegisteredInSystem(TableNode $users)
    {
        throw new PendingException();
    }

    /**
     * @Then I can't see any other list than list :name
     */
    public function iCantSeeAnyOtherListThanList($name)
    {
        throw new PendingException();
    }
}
