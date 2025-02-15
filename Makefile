isDocker := $(shell docker info > /dev/null 2>&1 && echo 1)
isProd := $(shell grep "APP_ENV=prod" .env.local > /dev/null && echo 1)
domain := "tes.shoko-cosplay.fr"
server := "shokoCosplay"
user := $(shell id -u)
group := $(shell id -g)

sy := php bin/console
bun :=
php :=
ifeq ($(isDocker), 1)
	ifneq ($(isProd), 1)
		dc := USER_ID=$(user) GROUP_ID=$(group) docker compose
		dcimport := USER_ID=$(user) GROUP_ID=$(group) docker compose -f docker-compose.import.yml
		de := docker compose exec
		dr := $(dc) run --rm
		drtest := $(dc) -f docker-compose.test.yml run --rm
		sy := $(de) php bin/console
		bun := $(dr) bun
		php := $(dr) --no-deps php
	endif
endif

.DEFAULT_GOAL := help
help: ## Affiche cette aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

build-docker:
	$(dc) pull --ignore-pull-failures
	$(dc) build php
	$(dc) build messenger

dev: node_modules/time ## Lance le serveur de développement
	$(dc) up

seed: vendor/autoload.php ## Génère des données dans la base de données (docker compose up doit être lancé)
	$(sy) doctrine:migrations:migrate -q
	$(sy) app:seed -q

migration: vendor/autoload.php ## Génère les migrations
	$(sy) make:migration

migrate: vendor/autoload.php ## Migre la base de données (docker compose up doit être lancé)
	$(sy) doctrine:migrations:migrate -q

rollback:
	$(sy) doctrine:migration:migrate prev

test: vendor/autoload.php node_modules/time ## Execute les tests
	echo "APP_ENV=dev" => .env.local
	$(drtest) phptest bin/console doctrine:schema:validate --skip-sync
	$(drtest) phptest vendor/bin/paratest -p 4 --runner=WrapperRunner

tt: vendor/autoload.php ## Lance le watcher phpunit
	$(drtest) phptest bin/console doctrine:schema:validate --skip-sync
	$(drtest) phptest bin/phpunit
	# $(drtest) phptest bin/console cache:clear --env=test
	# $(drtest) phptest vendor/bin/phpunit-watcher watch --filter="nothing"

lint: vendor/autoload.php ## Analyse le code
	docker run -v $(PWD):/app -w /app -t --rm ghcr.io/shoko-cosplay/sc-bondage-docker:master php -d memory_limit=-1 bin/console lint:container
	docker run -v $(PWD):/app -w /app -t --rm ghcr.io/shoko-cosplay/sc-bondage-docker:master php -d memory_limit=-1 ./vendor/bin/phpstan analyse

security-check: vendor/autoload.php ## Check pour les vulnérabilités des dependencies
	$(de) php local-php-security-checker --path=/var/www

format: ## Formate le code
	bunx prettier-standard --lint --changed "assets/**/*.{js,css,jsx}"
	docker run -v $(PWD):/app -w /app -t --rm ghcr.io/shoko-cosplay/sc-bondage-docker:master php -d memory_limit=-1 ./vendor/bin/php-cs-fixer fix

refactor: ## Reformate le code avec rector
	docker run -v $(PWD):/app -w /app -t --rm ghcr.io/shoko-cosplay/sc-bondage-docker:master php -d memory_limit=-1 ./vendor/bin/rector process src

doc: ## Génère le sommaire de la documentation
	npx doctoc ./README.md

routes:
	$(de) php bin/console cache:clear

# -----------------------------------
# Dépendances
# -----------------------------------
vendor/autoload.php: composer.lock
	$(php) composer install
	touch vendor/autoload.php

node_modules/time: bun.lockb
	$(bun) bun install
	touch node_modules/time

bun.lockb:
	$(bun) bun install

public/assets: node_modules/time
	$(bun) run build

var/dump:
	mkdir var/dump

public/assets/.vite/manifest.json: package.json
	$(bun) bun install
	$(bun) bun run build