API_PLACEMARKERS_SEARCH_DIR := $(patsubst %/,%,$(dir $(abspath $(lastword $(MAKEFILE_LIST)))))
PROJECT_ROOT := $(abspath $(API_PLACEMARKERS_SEARCH_DIR)/../..)
include $(PROJECT_ROOT)/config.mk

.PHONY: api-placemarkers-search-init api-placemarkers-search-build api-placemarkers-search-up api-placemarkers-search-down api-placemarkers-search-test-unit

api-placemarkers-search-init:
	@echo "composer зависимости"
	docker compose -f $(API_PLACEMARKERS_SEARCH_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) run --rm api-placemarkers-search-php-cli composer install --optimize-autoloader --no-interaction
	@echo 'Обновляю автозагрузчик Composer...';
	docker compose -f $(API_PLACEMARKERS_SEARCH_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) run --rm api-placemarkers-search-php-cli composer dump-autoload --optimize;
	@echo 'Генерирую JWT ключи...';
	docker compose -f $(API_PLACEMARKERS_SEARCH_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) run --rm api-placemarkers-search-php-cli php bin/console lexik:jwt:generate-keypair --skip-if-exists

api-placemarkers-search-build:
	@echo build api-placemarkers-search
	docker compose -f $(API_PLACEMARKERS_SEARCH_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) build

api-placemarkers-search-up:
	@echo up api-placemarkers-search
	docker compose -f $(API_PLACEMARKERS_SEARCH_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) up -d api-placemarkers-search

api-placemarkers-search-down:
	@echo down api-placemarkers-search
	docker compose -f $(API_PLACEMARKERS_SEARCH_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) down -v

api-placemarkers-search-test-unit:
	docker compose -f $(API_PLACEMARKERS_SEARCH_DIR)/docker-compose.yaml -p $(PROJECT_GROUP_MAIN_SERVICE) run --rm api-placemarkers-search-php-cli vendor/bin/phpunit
