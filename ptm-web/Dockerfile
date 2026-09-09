FROM php:8.4.20-apache

# System packages and PHP extension build dependencies
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        gd \
        intl \
        opcache \
        pdo_mysql \
        zip \
    && rm -rf /var/lib/apt/lists/*

# Laravel requires mod_rewrite for normal Apache routing.
RUN a2enmod rewrite

# Laravel's web root MUST be /public rather than the project root.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri \
        "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" \
        /etc/apache2/sites-available/*.conf \
    && sed -ri \
        "s!<Directory /var/www/>!<Directory ${APACHE_DOCUMENT_ROOT}/>!g" \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/*.conf

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html