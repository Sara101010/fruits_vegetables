# API Documentation

API to add and list vegetables and fruits.

## Table of Contents

- **Endpoints:**
    - [POST /food](endpoints/post-food.md)
    - [GET /food](endpoints/get-food.md)
- **Docker:**
    - Run ```docker-compose build``` and ```docker-compose up```
    - To enter the container: ```docker exec -it fruits-and-vegetables-main_php-fpm_1 sh```
    - To execute the tests: ```cd ../var/www & bin/phpunit```