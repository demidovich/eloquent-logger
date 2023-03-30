SHELL = /bin/bash -o pipefail
.DEFAULT_GOAL := help

docker_bin := $(shell command -v docker 2> /dev/null)
docker_compose_bin := $(shell command -v docker-compose 2> /dev/null)

all_images = eloquent-logger-php

help: ## This help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

up: ## Up containers
	$(docker_compose_bin) up -d --force-recreate --build

down: ## Down containers
	$(docker_compose_bin) down

clean: clean ## Remove all images
	$(foreach image,$(all_images),$(docker_bin) rmi -f $(image);)

ps: ## Status of containers
	$(docker_compose_bin) ps

log: ## Output log of containers
	$(docker_compose_bin) --follow --tail 1

php: ## Shell of php container
	$(docker_compose_bin) exec --user root php /bin/sh

