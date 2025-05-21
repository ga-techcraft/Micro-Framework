# PHP Dockerfile for Apache (optional with extensions)
# This example uses php-fpm
FROM php:8.1-fpm

# memcached拡張のインストールに必要な依存を全て含める
RUN apt-get update && apt-get install -y \
    libmemcached-dev \
    zlib1g-dev \
    libssl-dev \
    pkg-config \
    libzstd-dev \
    libsasl2-dev \
    gcc \
    g++ \
    make \
    autoconf \
    git \
    zip \
    unzip \
    && pecl install memcached \
    && docker-php-ext-enable memcached \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions and dependencies
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Composerをコピーして利用できるようにする（公式から）
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html