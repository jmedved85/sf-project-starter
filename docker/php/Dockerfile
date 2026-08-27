FROM php:8.4-fpm-bookworm

# Install Composer in the PHP image so dependency management works inside the container.
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Useful PHP extension installer image, copy binary into your container
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install runtime and development utilities.
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        acl \
        bash \
        ca-certificates \
        curl \
        libfcgi-bin \
        file \
        fontconfig \
        fonts-dejavu \
        gettext \
        git \
        libgmp10 \
        libjpeg62-turbo \
        libpng16-16 \
        libssl3 \
        libstdc++6 \
        libx11-6 \
        libxext6 \
        libxrender1 \
        nano \
        sudo \
        tar \
        wget \
        wkhtmltopdf \
        zlib1g \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN set -eux; \
    install-php-extensions \
        intl \
        zip \
        apcu \
        opcache \
        pdo pdo_mysql \
        sqlite3 \
        dom \
        mbstring \
        gd \
        gmp \
        bcmath \
        exif \
        sockets \
        pcntl \
        xml \
        xmlwriter \
        simplexml

# Copy custom php.ini
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# Set www-data UID/GID to match host user
ARG USER_ID=1000
ARG GROUP_ID=1000
RUN usermod -u ${USER_ID} www-data && \
    groupmod -g ${GROUP_ID} www-data

# Copy entrypoint script
COPY ./docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set working directory
WORKDIR /var/www/html

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]