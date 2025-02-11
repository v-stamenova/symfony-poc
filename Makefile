CONTAINER_NAME=symfony_app
DB_FILE=$(shell php -r "echo str_replace('sqlite:///%kernel.project_dir%/', '$(shell pwd)/', getenv('DATABASE_URL')) ?: 'var/data/db.sqlite';")

composer-install:
	docker exec -it $(CONTAINER_NAME) composer require $(PACKAGE)

phpunit:
	docker exec -it $(CONTAINER_NAME) ./vendor/bin/phpunit

phpstan:
	docker exec  -it $(CONTAINER_NAME) ./vendor/bin/phpstan analyse -c phpstan.neon

bash:
	docker exec -it $(CONTAINER_NAME) bash

migrate:
	docker exec -it $(CONTAINER_NAME) php bin/console doctrine:migrations:migrate --no-interaction

migrate-fresh:
	rm -f $(DB_FILE)
	touch $(DB_FILE)
	chmod 777 $(DB_FILE)
	docker exec -it $(CONTAINER_NAME) php bin/console doctrine:migrations:migrate --no-interaction