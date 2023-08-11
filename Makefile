up:
	docker compose up -d

app_ssh:
	docker compose exec app bash

db_ssh:
	docker compose exec db bash

build:
	docker compose up -d --build

down:
	docker compose down

stop:
	docker compose stop

restart:
	docker compose restart

list:
	docker compose list