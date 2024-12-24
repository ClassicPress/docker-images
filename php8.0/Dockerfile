#
# NOTE: THIS DOCKERFILE IS GENERATED VIA "apply-templates.sh"
#
# PLEASE DO NOT EDIT IT DIRECTLY.
#

FROM php:8.0-apache

# Persistent dependencies
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
    ghostscript \
    libfreetype6-dev \
    libicu-dev \
    libjpeg-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    libmagickwand-dev \
    zip \
    unzip \
    ; \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN set -eux; \
    docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp; \
    docker-php-ext-install -j "$(nproc)" \
        bcmath \
        exif \
        gd \
        intl \
        mysqli \
        zip; \
    docker-php-ext-enable zip

# Install imagick manually
RUN set -eux; \
    curl -fL -o imagick.tgz "https://pecl.php.net/get/imagick-3.5.1.tgz"; \
    mkdir -p /tmp/imagick; \
    tar -xvzf imagick.tgz -C /tmp/imagick --strip-components=1; \
    rm imagick.tgz; \
    docker-php-ext-configure /tmp/imagick; \
    docker-php-ext-install /tmp/imagick; \
    docker-php-ext-enable imagick; \
    rm -rf /tmp/imagick

# Validate modules
RUN set -eux; \
    php -m | grep -E 'zip|imagick'; \
    php -r 'if (!extension_loaded("zip")) exit(1); if (!extension_loaded("imagick")) exit(1);'

# Recommended PHP.ini settings
RUN set -eux; \
    docker-php-ext-enable opcache; \
    { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=2'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Error logging
RUN { \
    echo 'error_reporting = E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_RECOVERABLE_ERROR'; \
    echo 'display_errors = Off'; \
    echo 'display_startup_errors = Off'; \
    echo 'log_errors = On'; \
    echo 'error_log = /dev/stderr'; \
    echo 'log_errors_max_len = 1024'; \
    echo 'ignore_repeated_errors = On'; \
    echo 'ignore_repeated_source = Off'; \
    echo 'html_errors = Off'; \
    } > /usr/local/etc/php/conf.d/error-logging.ini

# Apache modules
RUN set -eux; \
    a2enmod rewrite expires; \
    a2enmod remoteip; \
    { \
        echo 'RemoteIPHeader X-Forwarded-For'; \
        echo 'RemoteIPInternalProxy 10.0.0.0/8'; \
        echo 'RemoteIPInternalProxy 172.16.0.0/12'; \
        echo 'RemoteIPInternalProxy 192.168.0.0/16'; \
        echo 'RemoteIPInternalProxy 169.254.0.0/16'; \
        echo 'RemoteIPInternalProxy 127.0.0.0/8'; \
    } > /etc/apache2/conf-available/remoteip.conf; \
    a2enconf remoteip

# Download and prepare ClassicPress
RUN set -eux; \
    curl -o classicpress.tar.gz -fL "https://github.com/ClassicPress/ClassicPress-release/archive/refs/tags/2.3.1.tar.gz"; \
    tar -xzf classicpress.tar.gz -C /usr/src/; \
    mv /usr/src/ClassicPress-release-2.3.1 /usr/src/classicpress; \
    rm classicpress.tar.gz; \
    chown -R www-data:www-data /usr/src/classicpress; \
    mkdir wp-content; \
    chown -R www-data:www-data wp-content; \
    chmod -R 1777 wp-content

# Volume and entrypoint
VOLUME /var/www/html
COPY --chown=www-data:www-data php8.0/wp-config-docker.php /usr/src/classicpress/
COPY php8.0/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
