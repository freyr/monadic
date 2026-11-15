.PHONY: shell install test stan cs check coverage infection example

shell:
	docker compose run --rm php sh

install:
	docker compose run --rm php composer update

test:
	docker compose run --rm php composer test

stan:
	docker compose run --rm php composer stan

cs:
	docker compose run --rm php composer cs

check:
	docker compose run --rm php composer check

coverage:
	docker compose run --rm php composer coverage

infection:
	docker compose run --rm php composer infection

example:
	docker compose run --rm php composer example
