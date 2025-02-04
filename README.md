# Symfony CRUD PoC
A small task tracking application that supports all the basic CRUD functionality.

## Set-up
> **Note**: Definitely not a nice set-up. If you plan a further use do not use this set up. This is just a quick set-up that gets the job done.

1. Clone the application on your WSL (if you are on Windows)
2. Open your console and go to the application root folder and run
```dockerfile
docker rit --rm -v .:/app -w /app composer:latest bash
```
3. The container contains the latest composer image that allows you to install all of the dependencies needed to start the app. Run `composer install` within the container
4. Run the host the application
```dockerfile
docker r-rm -v .:/app -w /app -p 8000:8000 php:cli php -S 0.0.0.0:8000 -t /app/public
```
5. Run `mkdir var/data/`
6. Run `touch var/data/db.sqlite`
7. Using the sqlite3 CLI, open the file and run
```sql
sqlite3 var/data/database.sqlite <<EOF
CREATE TABLE tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT)
```
8. You should be able to see the Symfony main page and able to go to `/tasks`

Once again - just a PoC (Proof of Concept). Set-up is at best ugly; code is even uglier.