.PHONY: up down reset test

up:
	@test -f .env || cp .env.example .env
	docker compose up -d --build --wait

down:
	docker compose down

# Destroys only this project's database volume, the schema and seed will reload on start.
reset:
	docker compose down -v
	$(MAKE) up

# Unit tests need no database. Composer and PHPUnit run in a throwaway container.
test:
	docker run --rm -v "$(CURDIR)":/app -w /app composer:2 sh -c "composer install --no-interaction --quiet && vendor/bin/phpunit"
