COMPOSE := $(shell docker compose version >/dev/null 2>&1 && echo docker compose || echo docker-compose)

.PHONY: up down logs migrate test shell-api shell-web

up:
	$(COMPOSE) up --build

down:
	$(COMPOSE) down

logs:
	$(COMPOSE) logs -f

migrate:
	$(COMPOSE) exec backend php artisan migrate --seed --force

test:
	$(COMPOSE) exec backend php artisan test

shell-api:
	$(COMPOSE) exec backend bash

shell-web:
	$(COMPOSE) exec frontend bash
