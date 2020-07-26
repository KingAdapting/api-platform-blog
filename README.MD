# How to run
1. Start docker containers:
    ```
    docker-compose up --build -d
    ```
2. Install dependencies
    ```
    docker-compose exec php-fpm composer install
    ```
3. Execute migrations
   ```
   docker-compose exec php-fpm php bin/console doctrine:migrations:migrate -n
   ``` 
4. Create ssl keys for JWT
   ```
   docker-compose exec php-fpm sh -c '
       set -e
       mkdir -p config/jwt
       jwt_passphrase=${JWT_PASSPHRASE:-$(grep ''^JWT_PASSPHRASE='' .env | cut -f 2 -d ''='')}
       echo "$jwt_passphrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
       echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout
   '
   ```
# Testing
1. Create separate database for testing
    ```
    docker-compose exec php-fpm php bin/console doctrine:database:create --env=test
    ```
2. Update schema
    ```
    docker-compose exec php-fpm php bin/console doctrine:schema:update --force --env=test
    ```
3. Load test fixtures
    ```
    docker-compose exec php-fpm php bin/console doctrine:fixtures:load --no-interaction --env=test
    ```
4. Run tests
    ```
    docker-compose exec php-fpm php bin/phpunit
    ```