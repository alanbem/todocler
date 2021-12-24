Feature: Creating lists and tasks
  In order to perform my tasks one by one
  As a user
  I want to have an ability to see a list of tasks for my day

  Background:
    Given there are users registered in system:
    | email             | password    |
    | adsam@example.com | my_password |
    | john@example.com  | my_password |

  Scenario: Creating list
    Given I am signed in as "john@example.com" with password "password"
    When I create new list "Work"
    Then I can see list named "Work"
    And I can't see any other list than list "Work"
    And list "Work" has no tasks yet
    When I create new task "Read emails" under "Work" list
    When I create new task "Prepare Q1 report" under "Work" list
    Then list "Work" has task "Read emails" assigned
    And list "Work" has task "Prepare Q1 report" assigned
    And task "Read emails" under list "Work" is not yet completed
    And task "Prepare Q1 report" under list "Work" is not yet completed
    When I mark task "Prepare Q1 report" under list "Work" as completed
    And task "Prepare Q1 report" under list "Work" is completed
    But task "Read emails" under list "Work" is not yet completed
