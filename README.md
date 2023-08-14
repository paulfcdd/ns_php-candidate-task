# Bike-sharing application
## Requirements
- Docker v 20.10.21 or higher
- Docker Compose v 2.12.2 or higher

## Installation
- After cloning the repo go to repo folder and run `make build`
- After build type `make app_ssh`
- Create a copy of `.env` file `cp .evn .env.dev` and define `DATABASE_URL` parameter. Make sure you have set your env to `dev`. Version of MySQL used in container - `8.0.32`, credentials to connect to db container - `root/mysql`
- Run `composer install`
- After provide your DB connection parameters run `bin/console doctrine:database:create`
- After database created run migrations `bin/console doctrine:migrations:migrate`
- After database created and all necessary tables created run `bin/console app:sync-networks` to sync network data
- Go to `http://localhost` and enjoy :)

## Testing
- Type `make app_ssh`
- Run `./vendor/bin/phpunit tests`