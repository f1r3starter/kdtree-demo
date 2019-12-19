FROM php:7.2-cli-alpine

RUN apk update && apk add build-base

RUN apk add zlib-dev git zip \
  && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php \
        && mv composer.phar /usr/local/bin/ \
        && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

ADD ./ /project

RUN composer install -d /project --prefer-source --no-interaction

CMD ["php", "/project/index.php"]

EXPOSE 8080
