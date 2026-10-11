.PHONY: up down reset

up:
	@test -f .env || cp .env.example .env
	docker compose up -d --build --wait

down:
	docker compose down

# Destroys only this project's database volume, the schema and seed will reload on start.
reset:
	docker compose down -v
	$(MAKE) up
