# Docker
build:
	@docker compose build

up:
	@docker compose up -d

restart:
	@docker compose restart

down:
	@docker compose down

# PHP
composer-install:
	@docker compose exec php composer install

db-init:
	@docker compose exec php symfony console doctrine:migrations:migrate

fixtures-load:
	@docker compose exec php symfony console doctrine:fixtures:load

cmd-php:
	@docker compose exec php bash

# Node
yarn-install:
	@docker compose exec node yarn install

yarn-watch:
	@docker compose exec node yarn watch

cmd-node:
	@docker compose exec node bash

# Certificates
generate-certs:
	@mkdir -p ./docker-dev/certs
	@chmod 777 ./docker-dev/certs
	@openssl req -x509 -nodes -days 3650 -newkey rsa:2048 \
	  -keyout ./docker-dev/certs/localhost.key -out ./docker-dev/certs/localhost.crt \
	  -subj "/C=FR/ST=Dev/L=Local/O=Localhost/CN=localhost"
	@echo "Certificates generated in /certs"
