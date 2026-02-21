DOCTRINE = docker compose run --rm php php config/doctrine/doctrine-orm.php
MIGRATIONS = docker compose run --rm php vendor/bin/doctrine-migrations --configuration=config/doctrine/cli-config.php

.PHONY: up down build restart logs shell install ps db \
        doctrine-validate doctrine-schema-create doctrine-schema-update doctrine-schema-drop \
        migration-diff migration-migrate migration-status migration-rollback \
        test test-unit test-integration

# ── Docker ────────────────────────────────────────────────────────────────────

up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build --no-cache

restart:
	docker compose restart

install:
	docker compose run --rm php composer install

logs:
	docker compose logs -f

ps:
	docker compose ps

shell:
	docker compose exec php bash

db:
	docker compose exec mysql bash -c 'mysql -u $$MYSQL_USER -p$$MYSQL_PASSWORD $$MYSQL_DATABASE'

kill:
	docker compose kill

# ── Tests ─────────────────────────────────────────────────────────────────────

test:
	docker compose run --rm php vendor/bin/phpunit

test-unit:
	docker compose run --rm php vendor/bin/phpunit --testsuite Unit

test-integration:
	docker compose run --rm php vendor/bin/phpunit --testsuite Integration

# ── Doctrine ORM ──────────────────────────────────────────────────────────────

doctrine-validate:
	$(DOCTRINE) orm:validate-schema

doctrine-schema-create:
	$(DOCTRINE) orm:schema-tool:create

doctrine-schema-update:
	$(DOCTRINE) orm:schema-tool:update --force

doctrine-schema-drop:
	$(DOCTRINE) orm:schema-tool:drop --force

# ── Migrations ────────────────────────────────────────────────────────────────

migration-diff:
	$(MIGRATIONS) diff

migration-migrate:
	$(MIGRATIONS) migrate --no-interaction

migration-status:
	$(MIGRATIONS) status

migration-rollback:
	$(MIGRATIONS) rollback latest
