CONTAINER_NAME=symfony_app

composer-install:
	docker exec -it $(CONTAINER_NAME) composer require $(PACKAGE)

phpunit:
	docker exec -it $(CONTAINER_NAME) ./vendor/bin/phpunit

bash:
	docker exec -it $(CONTAINER_NAME) bash