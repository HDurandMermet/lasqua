FROM php:7.4-fpm-alpine

ENV EXT_APCU_VERSION=5.1.17

RUN apk add --no-cache nginx supervisor wget autoconf gcc libc6-compat g++ build-base curl grpc musl libstdc++ libgcc libprotobuf zlib php7-common  protobuf grpc-cli linux-headers zlib zlib-dev

RUN mkdir -p /run/nginx

COPY docker/nginx.conf /etc/nginx/nginx.conf

RUN pecl install grpc-1.27.0 && docker-php-ext-enable grpc
RUN mkdir -p /app
COPY . /app

RUN sh -c "wget http://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer"
RUN cd /app && \
    /usr/local/bin/composer install --no-dev

RUN chown -R www-data: /app

CMD sh /app/docker/startup.sh
