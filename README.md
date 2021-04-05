# TODOcler

[![codecov](https://codecov.io/gh/alanbem/todocler/branch/main/graph/badge.svg?token=O5WFLBW4EZ)](https://codecov.io/gh/alanbem/todocler)

Run `./docker/build.sh` to build project and load fixtures. You might have to run it with `sudo` depending on your system configuration.

Open `http://127.0.0.1:8080` to get access to Swagger UI.

Authenticate with one of 2 already existing users

- `adam@example.com`/`password`
- `john@example.com`/`password`

or register your own user running `bin/console todocler:users:register-user [uuid] [email] [password]` console command.

Receive authentication token via `/auth/token` endpoint. That token MUST be supplied with every request as a header `Authorization: Bearer <token>` - Swagger UI allows to setup that header using modal shown after clicking `Authorize` button (you can find it at the top of the page). Just open the modal and paste `Bearer <token>` into the input.
