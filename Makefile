MAIN_DIR := $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))
SHELL := /usr/bin/env bash

help: ## Print this help
	@grep -E '^[a-zA-Z1-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| sort \
		| awk 'BEGIN { FS = ":.*?## " }; { printf "\033[36m%-30s\033[0m %s\n", $$1, $$2 }'

install: ## Install everything
	$(info --> Install everything)
	@$(MAKE) docker-up
	@$(MAKE) wait-up
	@$(MAKE) install-wordpress
	@$(MAKE) install-wordpress-multisite
	@$(MAKE) activate-plugin
	@$(MAKE) activate-plugin-multisite

docker-up: ## Simply run docker-compose up -d
	$(info --> Simply run docker-compose up -d)
	@docker-compose up -d

wait-up: ## Wait for services to be up
	$(info --> Wait for services to be up)
	@while [ "$$(docker inspect -f {{.State.Health.Status}} db)" != "healthy" ]; do \
		echo 'db is not ready yet'; sleep 1; \
		done
	@while [ "$$(docker inspect -f {{.State.Health.Status}} wordpress)" != "healthy" ]; do \
		echo 'wordpress is not ready yet'; sleep 1; \
		done
	@while [ "$$(docker inspect -f {{.State.Health.Status}} wordpress-multisite)" != "healthy" ]; do \
		echo 'wordpress-multisite is not ready yet'; sleep 1; \
		done

install-wordpress: ## Run WordPress installer using cli
	$(info --> Run WordPress installer using cli)
	@docker run -it \
		--rm --volumes-from wordpress \
		--network container:wordpress \
		wordpress:cli \
			core install \
			--url=localhost:8080 \
			--title="dev" \
			--admin_user="admin" \
			--admin_password="notsecurepassword" \
			--admin_email="nierdz@example.com"

install-wordpress-multisite: ## Run WordPress Multisite installer using cli
	$(info --> Run WordPress Multisite installer using cli)
	@docker run -it \
		--rm --volumes-from wordpress-multisite \
		--network container:wordpress-multisite \
		wordpress:cli \
			core multisite-install \
			--title="dev multisite" \
			--admin_user="admin" \
			--admin_password="notsecurepassword" \
			--admin_email="nierdz@example.com"

activate-plugin: ## Activate WP-OPcache on WordPress
	$(info --> Activate WP-OPcache on WordPress)
	@docker run -it \
		--rm --volumes-from wordpress \
		--network container:wordpress \
		wordpress:cli \
			plugin activate \
			flush-opcache

activate-plugin-multisite: ## Activate WP-OPcache on WordPress Multisite
	$(info --> Activate WP-OPcache on WordPress Multisite)
	@docker run -it --rm \
		--volumes-from wordpress-multisite \
		--network container:wordpress-multisite \
		wordpress:cli \
			plugin activate \
			flush-opcache
