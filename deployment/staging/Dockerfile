FROM php:8.2-fpm-bullseye

# Install system dependencies
RUN apt-get update -y && \
    apt-get install --no-install-recommends -y unzip default-mysql-client \
        libicu-dev \
        libxslt1-dev \
        supervisor \
        procps \
        vim \
        less \
        zlib1g-dev \
        libpng-dev \
        libzip-dev \
        && \
    mkdir -p /var/log/supervisor && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-install intl bcmath pdo pdo_mysql gd zip && \
    apt-get clean


COPY --from=caddy:2.6.4 /usr/bin/caddy /usr/bin/caddy

# app entrypoint
ADD /deployment/staging/entrypoint.sh /
ADD /deployment/config-files/memory-limit-php.ini /usr/local/etc/php/conf.d/memory-limit-php.ini

RUN rm -Rf /usr/local/etc/php-fpm.*
ADD /deployment/config-files/php-fpm.conf /usr/local/etc/php-fpm.conf

ADD /deployment/config-files/Caddyfile /etc/caddy/Caddyfile
RUN mkdir -p /usr/local/var/log/ && \
    chown -R www-data /usr/local/var/log/ && \
    chown -R www-data /var/www/

# cleanup & chown
RUN mkdir -p /storage/app /composer_cache && \
    chown -R www-data /storage/app /composer_cache

# add the App files as www-data. For high security environments, you would try remove permissions from the app files.
ADD --chown=www-data:www-data /. /


WORKDIR /
USER www-data

ENTRYPOINT [ "/entrypoint.sh" ]
