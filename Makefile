# Makefile
# Использование:
#   make up ENV=dev
#   make down ENV=staging
#   make build ENV=prod
#   make up-all / make down-all
#   make ps ENV=dev
#   make logs ENV=dev SERVICE=app
#   make migrate-status ENV=dev
#   make migrate-status-all

SHELL := /bin/bash

ENV ?= dev
SERVICE ?=
APP_SERVICE ?= app

COMPOSE_FILE := docker-compose.yml
ENV_FILE := .env.compose.$(ENV)

# Проверка, что файл окружения существует
define check_env
	@test -f $(ENV_FILE) || (echo "❌ $(ENV_FILE) not found. Use ENV=dev|staging|prod"; exit 1)
endef

.PHONY: help up down build restart ps logs pull config up-all down-all build-all \
        migrate migrate-status migrate-status-all

help:
	@echo "Usage:"
	@echo "  make up      ENV=dev|staging|prod   # docker compose up -d"
	@echo "  make down    ENV=dev|staging|prod   # docker compose down"
	@echo "  make build   ENV=dev|staging|prod   # docker compose build"
	@echo "  make restart ENV=dev|staging|prod   # down + up"
	@echo "  make ps      ENV=dev|staging|prod   # docker compose ps"
	@echo "  make logs    ENV=dev|staging|prod SERVICE=app"
	@echo "  make pull    ENV=dev|staging|prod   # docker compose pull"
	@echo "  make config  ENV=dev|staging|prod   # validate rendered compose"
	@echo "  make migrate         ENV=dev|staging|prod   # php artisan migrate"
	@echo "  make migrate-status  ENV=dev|staging|prod   # php artisan migrate:status"
	@echo "  make up-all / down-all / build-all  # for dev+staging+prod"

# ---------- single env ----------

up:
	$(call check_env)
	set -a; source .env; set +a; \
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) up -d

down:
	$(call check_env)
	set -a; source .env; set +a; \
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) down

build:
	$(call check_env)
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) build

restart: down up

ps:
	$(call check_env)
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) ps

logs:
	$(call check_env)
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) logs -f $(SERVICE)

pull:
	$(call check_env)
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) pull

config:
	$(call check_env)
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) config -q
	@echo "✅ $(ENV) compose OK"

# ---------- migrations ----------

migrate:
	$(call check_env)
	set -a; source .env; set +a; \
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) exec -T $(APP_SERVICE) php artisan migrate --force

migrate-status:
	$(call check_env)
	set -a; source .env; set +a; \
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) exec -T $(APP_SERVICE) php artisan migrate:status

migrate-status-all:
	@for e in dev staging prod; do \
		echo "▶ migrate:status $$e"; \
		$(MAKE) migrate-status ENV=$$e || exit 1; \
		echo; \
	done

# ---------- all envs ----------

up-all:
	@for e in dev staging prod; do \
		echo "▶ up $$e"; \
		$(MAKE) up ENV=$$e || exit 1; \
	done

down-all:
	@for e in prod staging dev; do \
		echo "▶ down $$e"; \
		$(MAKE) down ENV=$$e || exit 1; \
	done

build-all:
	@for e in dev staging prod; do \
		echo "▶ build $$e"; \
		$(MAKE) build ENV=$$e || exit 1; \
	done
