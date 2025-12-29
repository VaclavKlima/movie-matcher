FROM php:8.2-cli

# Accept Git information as build arguments
ARG GIT_COMMIT=unknown
ARG GIT_BRANCH=main

# Set Git info as environment variables for runtime access
ENV GIT_COMMIT=${GIT_COMMIT}
ENV GIT_BRANCH=${GIT_BRANCH}

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        curl \
        libicu-dev \
        libonig-dev \
        libsqlite3-dev \
        libxml2-dev \
        libzip-dev \
        nodejs \
        npm \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j$(nproc) \
        intl \
        mbstring \
        pdo \
        pdo_sqlite \
        zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/notify-discord.sh /usr/local/bin/notify-discord.sh
RUN chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/notify-discord.sh

ENTRYPOINT ["entrypoint.sh"]
