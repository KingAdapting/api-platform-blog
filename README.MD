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