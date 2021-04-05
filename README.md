# TODOcler

[![codecov](https://codecov.io/gh/alanbem/todocler/branch/main/graph/badge.svg?token=O5WFLBW4EZ)](https://codecov.io/gh/alanbem/todocler)

Run `./docker/build.sh`

Open `http://127.0.0.1:8080`

Authenticate via `/auth/token/` endpoint with one of 2 already registered users

- `adam@example.com`/`password`
- `john@example.com`/`password`

or register your own user running `bin/console todocler:users:register-user [uuid] [email] [password]` command.

