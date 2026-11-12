# Makefile
# Использование:
#   make up ENV=dev
#   make down ENV=staging
#   make build ENV=prod
#   make up-all / make down-all
#   make ps ENV=dev
#   make logs ENV=dev SERVICE=app
#   make migrate ENV=dev
#   make migrate-status-all
#   make health ENV=prod
#   make dump ENV=prod
#   make restore ENV=dev FILE=backup.sql
#   make optimize ENV=prod
#   make queue-restart ENV=prod
#   make tinker ENV=dev
#   make shell ENV=dev SERVICE=app

SHELL := /bin/bash

ENV ?= dev
SERVICE ?=
APP_SERVICE ?= app
DB_CONTAINER ?= mysql-dev          # ← контейнер БД (внешний, не в compose)
COMPOSE_FILE := docker-compose.yml
ENV_FILE := .env.compose.$(ENV)
BACKUP_DIR ?= ./backups
DUMP_FILE ?= $(BACKUP_DIR)/$(ENV)_$(shell date +%Y%m%d_%H%M%S).sql

# Проверка, что файл окружения существует
define check_env
	@test -f $(ENV_FILE) || (echo "❌ $(ENV_FILE) not found. Use ENV=dev|staging|prod"; exit 1)
endef

# Загрузка .env в shell (чтобы TGWS_*, REDIS_PASSWORD подставлялись без WARN)
define load_env
	set -a; source .env; set +a;
endef

# Защита от случайного down на prod
define confirm_prod
	@if [ "$(ENV)" = "prod" ]; then \
		read -p "⚠️  Really run '$(1)' on PROD? [y/N] " ans; \
		[ "$$ans" = "y" ] || (echo "aborted"; exit 1); \
	fi
endef

.PHONY: help up down build restart ps logs pull config up-all down-all build-all \
        migrate migrate-status migrate-status-all seed tinker shell health \
        optimize queue-restart dump restore rollout

help:
	@echo "Usage:"
	@echo "  make up      ENV=dev|staging|prod   # docker compose up -d"
	@echo "  make down    ENV=dev|staging|prod   # docker compose down (prod — с подтверждением)"
	@echo "  make build   ENV=dev|staging|prod   # docker compose build"
	@echo "  make restart ENV=dev|staging|prod   # down + up"
	@echo "  make ps      ENV=dev|staging|prod   # docker compose ps"
	@echo "  make logs    ENV=dev|staging|prod SERVICE=app"
	@echo "  make pull    ENV=dev|staging|prod   # docker compose pull"
	@echo "  make config  ENV=dev|staging|prod   # validate rendered compose"
	@echo "  make migrate         ENV=dev|staging|prod   # php artisan migrate --force"
	@echo "  make migrate-status  ENV=dev|staging|prod   # php artisan migrate:status"
	@echo "  make seed            ENV=dev|staging|prod   # php artisan db:seed --force"
	@echo "  make tinker          ENV=dev|staging|prod   # php artisan tinker"
	@echo "  make shell           ENV=dev|staging|prod SERVICE=app"
	@echo "  make health          ENV=dev|staging|prod   # curl /up"
	@echo "  make optimize        ENV=dev|staging|prod   # config/route/view/event cache"
	@echo "  make queue-restart   ENV=dev|staging|prod   # restart worker"
	@echo "  make dump            ENV=dev|staging|prod   # mysqldump --single-transaction"
	@echo "  make restore         ENV=dev|staging|prod FILE=path.sql"
	@echo "  make rollout         ENV=dev|staging|prod   # docker rollout (если установлен)"
	@echo "  make up-all / down-all / build-all / migrate-status-all"

# ---------- single env ----------

up:
	$(call check_env)
	$(call load_env) \
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) up -d

down:
	$(call check_env)
	$(call confirm_prod,down)
	$(call load_env) \
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

# ---------- migrations / artisan ----------

migrate:
	$(call check_env)
	$(call confirm_prod,migrate)
	$(call load_env) \
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) exec -T $(APP_SERVICE) php artisan migrate --force

migrate-status:
	$(call check_env)
	$(call load_env) \
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) exec -T $(APP_SERVICE) php artisan migrate:status

migrate-status-all:
	@for e in dev staging prod; do \
		echo "▶ migrate:status $$e"; \
		$(MAKE) migrate-status ENV=$$e || exit 1; \
		echo; \
	done

seed:
	$(call check_env)
	$(call confirm_prod,seed)
	$(call load_env) \
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) exec -T $(APP_SERVICE) php artisan db:seed --force

tinker:
	$(call check_env)
	$(call load_env) \
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) exec $(APP_SERVICE) php artisan tinker

shell:
	$(call check_env)
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) exec $(SERVICE) sh

optimize:
	$(call check_env)
	$(call confirm_prod,optimize)
	$(call load_env) \
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) exec -T $(APP_SERVICE) php artisan optimize

queue-restart:
	$(call check_env)
	$(call confirm_prod,queue-restart)
	$(call load_env) \
	docker compose --env-file $(ENV_FILE) -f $(COMPOSE_FILE) exec -T $(APP_SERVICE) php artisan queue:restart

# ---------- healthcheck ----------

health:
	$(call check_env)
	@case "$(ENV)" in \
		dev)     URL="https://blog-dev.sulfurfun.ru/up" ;; \
		staging) URL="https://blog-staging.sulfurfun.ru/up" ;; \
		prod)    URL="https://blog.sulfurfun.ru/up" ;; \
		*)       echo "❌ unknown ENV=$(ENV)"; exit 1 ;; \
	esac; \
	echo "▶ curl $$URL"; \
	curl -fsS -o /dev/null -w "%{http_code}\n" "$$URL" || (echo "❌ healthcheck failed"; exit 1)

# ---------- dump / restore ----------

dump:
	$(call check_env)
	@mkdir -p $(BACKUP_DIR)
	@echo "▶ dump $(ENV) via $(DB_CONTAINER) → $(DUMP_FILE)"
	$(call load_env) \
	docker exec -e MYSQL_PWD="$$DB_PASSWORD" \
	            -e DB_USERNAME="$$DB_USERNAME" \
	            -e DB_DATABASE="$$DB_DATABASE" \
	  $(DB_CONTAINER) sh -c '\
		mysqldump -h 127.0.0.1 -u "$$DB_USERNAME" \
		--single-transaction --no-tablespaces --routines --triggers --events "$$DB_DATABASE"' \
		> $(DUMP_FILE)
	@echo "✅ $(DUMP_FILE) ($$(du -h $(DUMP_FILE) | cut -f1))"

restore:
	$(call check_env)
	@test -n "$(FILE)" || (echo "❌ FILE=path.sql is required"; exit 1)
	@test -f "$(FILE)" || (echo "❌ $(FILE) not found"; exit 1)
	$(call confirm_prod,restore)
	@echo "▶ restore $(FILE) → $(ENV) via $(DB_CONTAINER)"
	$(call load_env) \
	docker exec -i -e MYSQL_PWD="$$DB_PASSWORD" \
	               -e DB_USERNAME="$$DB_USERNAME" \
	               -e DB_DATABASE="$$DB_DATABASE" \
	  $(DB_CONTAINER) sh -c '\
		mysql -h 127.0.0.1 -u "$$DB_USERNAME" "$$DB_DATABASE"' \
		< $(FILE)
	@echo "✅ restored $(FILE) → $(ENV)"

# ---------- rollout (zero-downtime, если установлен docker rollout) ----------

rollout:
	$(call check_env)
	$(call confirm_prod,rollout)
	@command -v docker-rollout >/dev/null 2>&1 || (echo "❌ docker-rollout not installed"; exit 1)
	$(call load_env) \
	docker rollout $(APP_SERVICE) --env-file $(ENV_FILE) -f $(COMPOSE_FILE)

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
