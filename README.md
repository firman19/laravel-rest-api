# REST API Blog

REST API built using Laravel 10.
This app contais following features:

1. Login
2. Register
3. Create blog
4. Filter blogs
5. Add comment in a blog
6. Like/dislike a blog
7. Like/dislike a comment
8. Edit and delete a blog/comment

## Run Locally

Clone the project

```bash
    git clone https://gitlab.com/firmanilham19/laravel-rest-api.git
```

Go to the project directory

```bash
    cd laravel-rest-api
```

Create .env file

```bash
    copy .env.example .env
```

Setup database in .env.
This is my setup

```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=rest-api
    DB_USERNAME=root
    DB_PASSWORD=
```

Install dependencies

```bash
    composer update
    composer install
```

Migrate database

```bash
    php artisan migrate
```

Generate key and install passport

```bash
    php artisan key:generate
    php artisan passport:install
    php artisan passport:keys
```

Start the server

```bash
    php artisan serve
```

To run the frontend, go to this repository

```bash
    https://gitlab.com/firmanilham19/vuejs-tailwind.git
```

## API Documentation

Go to the postman directory

```bash
    ./postman/rest-api.postman_collection.json
```

Import to postman and try register a new user and login.

Authentication uses Passport and Bearer token.
