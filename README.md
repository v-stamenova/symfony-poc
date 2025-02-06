# Symfony CRUD PoC
A small task tracking application that supports all the basic CRUD functionality.

## Set-up
1. Clone the application on your WSL if you are running on Windows
2. Navigate to the root folder and run `docker compose up -d`. The container will be created based on the Dockerfile
3. Open your container using `make bash` and run `composer install`
4. The application should be available at localhost:8000
5. If your database is new you should run 

Once again - just a PoC (Proof of Concept). Set-up is at best ugly; code is even uglier.