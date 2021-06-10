# TODOcler
### Over-engineered TODO application

[![codecov](https://codecov.io/gh/alanbem/todocler/branch/main/graph/badge.svg?token=O5WFLBW4EZ)](https://codecov.io/gh/alanbem/todocler)

## Installation

Run `./docker/build.sh` to build project and load fixtures. You might have to run it with `sudo` depending on your
system configuration.

Open `http://127.0.0.1:8080` to get access to Swagger UI.

Authenticate with one of 2 already existing users

- `adam@example.com`/`password`
- `john@example.com`/`password`

or register your own user running `bin/console todocler:users:register-user [uuid] [email] [password]` console command.

Receive authentication token via `/auth/token` endpoint. That token MUST be supplied with every request as a header
`Authorization: Bearer <token>` - Swagger UI allows to setup that header using modal shown after clicking `Authorize`
button (you can find it at the top of the page). Just open the modal and paste `Bearer <token>` into the input.

## Architecture overview

This Project is a modular monolith with 2 modules - which in this case aligns with **bounded contexts** - `Productivity` & `Users` and additionally
a very thin `Shared(Kernel)`. The separation (of logic) between modules is very naive because of the simple nature of application itself.
This architecture ensures that modules could be split into separated (micro-)services at any time without much refactoring needed.

Both modules are event-sourced and layered (DDD-style) while being driven by CQRS. They are loosely coupled, deploying **messaging** to make existing coupling one-directional (think: direct acyclic graph). 
Both modules share the same physical event store, but logically they are separated - it is just a pattern that allows for
easy debugging, because events coming from different services in the same log (store) are temporally monotonic (ordered).
Any other storage, besides aforementioned event store & messaging queue, is private to module using it.

### Users module
This module is responsible for creating users and indirectly for clients' authentication.

It provides internally:
- `RegisterUser` command
- `FindUser` query.
- `IsUserRegistered` query.

Data for those queries is provided by dedicated [RegisteredUsers](https://github.com/alanbem/todocler/blob/main/src/Users/Application/Projector/RegisteredUsers/Projector.php) projection. This projection uses Doctrine ORM for persistence.
Thanks to that with little to no effort it was also easy to integrate ([through configuration](https://github.com/alanbem/todocler/blob/main/config/packages/security.yaml#L7)) [`RegisteredUser` entity](https://github.com/alanbem/todocler/blob/main/src/Users/Application/Projector/RegisteredUsers/Doctrine/Entity/RegisteredUser.php) with Symfony security and
use it for authentication.

`Users` module has no outside dependencies and in order to make it stay that way it publishes integration events - via [another projection](https://github.com/alanbem/todocler/blob/main/src/Users/Application/Projector/Queue/Projector.php) - to external queue which later can be consumed by downstream clients... meaning other modules.
The underlying queue mechanism - albeit [abstracted](https://github.com/alanbem/todocler/blob/main/src/Users/Application/Projector/Queue/Projector/Queue.php) - uses [RabbitMQ](https://github.com/alanbem/todocler/blob/main/src/Users/Infrastructure/Queue/RabbitMQQueue.php) which also handles message deduplication (nice to have in at-least-once delivery environment).

Only a single console command [todocler:users:register-user](https://github.com/alanbem/todocler/blob/main/src/Users/Infrastructure/Interfaces/Console/Symfony/RegisterUserCommand.php) for registering new users is exposed as an outside interface of this module. Excluding authentication endpoint handled by Symfony.

I considered rewriting this module in a classic ORM-only way and showcase some safe inter-module messaging techniques (e.g. transactional outbox), but due to time constraints, I didn't.

### Productivity module
This module is responsible for TODO lists and theirs tasks.

It provides internally:
- `CreateList` command
- `RenameList` command
- `RemoveList` command
- `CreateTask` command
- `CompleteTask` command
- `RemoveTask` command
- `BrowseChecklists` query
- `BrowseTasks` query

Data for those queries is provided by dedicated [`Lists` projection](https://github.com/alanbem/todocler/blob/main/src/Productivity/Application/Projector/Lists/Projector.php). This projection uses Doctrine ORM for persistence.
Thanks to that it was possible to [integrate](https://github.com/alanbem/todocler/tree/main/src/Productivity/Infrastructure/Interfaces/Rest/ApiPlatform) [ApiPlatform](https://api-platform.com/) with projection entities and expose them as a configurable REST interface.

`Productivity` module has a dependency on `Users` module:
- One of the features of the application is that `Productivity` module must create first *welcoming* list (through [process manager](https://github.com/alanbem/todocler/blob/main/src/Productivity/Application/ProcessManager/Registration/ProcessManager.php#L56)) for the user as soon as possible after registration.
  I used dedicated [sensor](https://github.com/alanbem/todocler/blob/main/src/Productivity/Application/Sensor/UsersEvents/Sensor.php) for that - it translates any data into [internal events](https://github.com/alanbem/todocler/tree/main/src/Productivity/Application/Event). In this case, it's [wrapped around by
  RabbitMQ consumer](https://github.com/alanbem/todocler/blob/main/config/services/productivity/sensors.yaml#L23) and listens for AMQP messages that are published by `Users` module.
- Sometimes `Productivity` module has to retrieve details of a user or check whether user with given email exists.
  I could use above mentioned integration events from `Users` module and create local projection of registered user, but
  I chose different method: `Productivity` module declares [facade](https://github.com/alanbem/todocler/blob/main/src/Productivity/UsersFacade.php) with tight set of methods it requires from `Users` module.
  In the current [implementation](https://github.com/alanbem/todocler/blob/main/src/Users/Infrastructure/UsersFacadeForProductivity.php) it just runs internal queries, but in case of splitting the modules it could be easily swapped with HTTP implementation.
  This facade might serve as an anti-corruption layer in the future, when domain concepts (of a user) between our two modules start to noticeably diverge.

Except REST API powered by ApiPlatform this module exposes 2 console commands [todocler:productivity:create-list](https://github.com/alanbem/todocler/blob/main/src/Productivity/Infrastructure/Interfaces/Console/Symfony/CreateListCommand.php) and [todocler:productivity:create-task](https://github.com/alanbem/todocler/blob/main/src/Productivity/Infrastructure/Interfaces/Console/Symfony/CreateTaskCommand.php) as an outside interface.

### A word on event sourcing
Employing event sourcing has some drawbacks - mainly eventual consistency. Usually, eventual consistency is not a problem at all,
it is just a different way of thinking about data and its availability. Nevertheless, there are ways of dealing with technical dissonance resulting from EC, which I would be happy to discuss.

For event sourcing part of this project, I used [Streak](https://github.com/streakphp/streak) - framework supplying all the tools needed to work with
event-sourced aggregate roots, sagas/process managers, projections, etc. It helps to alleviate problems of transactions,
concurrency control, snapshotting and more.

## Docker
Everything you need to run this project with is dockerized. Please refer to [docker-compose.yaml](https://github.com/alanbem/todocler/blob/main/docker-compose.yaml) file and [docker/](https://github.com/alanbem/todocler/tree/main/docker) directory.

## Quality enforcing
Here are the tools I used to achieve the best quality possible.

### PHPUnit
Unit tests & their coverage are the first and foremost determinants of a quality. Please refer to [phpunit.xml.dist](https://github.com/alanbem/todocler/blob/main/phpunit.xml.dist) file and [tests/](https://github.com/alanbem/todocler/tree/main/tests) directory.

Run phpunit via `docker-compose run --rm php xphp bin/phpunit --color=always`
### Rector
Automated refactoring according to the set of configurable rules. Please refer to [rector.php](https://github.com/alanbem/todocler/blob/main/rector.php) file.

Run via `docker-compose run --rm --no-deps php bin/rector --ansi`
### Deptrac
Validates your topmost architecture, looking for dependencies where they should not be. Please refer to [depfile.yaml](https://github.com/alanbem/todocler/blob/main/depfile.yaml) file.

Run via `docker-compose run --rm --no-deps php bin/deptrac`
### PHP-CS-Fixer
Regulates coding standards. Especially useful for teams. Please refer to [.php_cs.dist](https://github.com/alanbem/todocler/blob/main/.php_cs.dist) file.

Run via `docker-compose run --rm --no-deps php bin/php-cs-fixer fix --diff`
### Continuous Integration pipeline
Runs all the above in tandem. I used Github Actions. Please refer to [.github/workflows/ci.yaml](https://github.com/alanbem/todocler/blob/main/.github/workflows/ci.yaml) file.

## What's missing
- GraphQL API
- Better REST API design - current solution due to ApiPlatform shortcomings is good enough, but IMHO suboptimal.
- Strong schema for integration events (protobuf, etc)
- BDD tests
- I would change `Checklist` to `Project` - unfortunately, `List` is a PHP keyword and can't be used as a class name. `Checklist` was the first thing I came up with.
