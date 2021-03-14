MAIN_DIR := $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))
VIRTUALENV_DIR := $(MAIN_DIR)/venv
VIRTUAL_ENV_DISABLE_PROMPT = true
PATH := $(VIRTUALENV_DIR)/bin:vendor/bin:$(PATH)
SHELL := /usr/bin/env bash

.DEFAULT_GOAL := help
.SHELLFLAGS := -eu -o pipefail -c

export PATH

help: ## Print this help
	@grep -E '^[a-zA-Z1-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| sort \
		| awk 'BEGIN { FS = ":.*?## " }; { printf "\033[36m%-30s\033[0m %s\n", $$1, $$2 }'

$(VIRTUALENV_DIR):
	virtualenv -p $(shell command -v python3) $(VIRTUALENV_DIR)

$(VIRTUALENV_DIR)/bin/pre-commit: $(MAIN_DIR)/requirements.txt
	pip install -r $(MAIN_DIR)/requirements.txt
	@touch '$(@)'

pre-commit-install: ## Install pre-commit hooks
	pre-commit install

install-pip-packages: $(VIRTUALENV_DIR) $(VIRTUALENV_DIR)/bin/pre-commit ## Install python pip packages in a virtual environment

install: install-pip-packages composer-install ## Install everything

composer-install: ## Install and setup wpcs
	composer install

tests: ## Run all phpcs tests
	phpcs \
		-v \
		--ignore=flush-opcache/admin/opcache.php,flush-opcache/admin/js/d3.min.js \
		--standard=WordPress \
		flush-opcache/
	pre-commit run --all-files
