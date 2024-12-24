FROM php:8.4-apache

# Install persistent dependencies
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        ghostscript \
        libavif-dev \
        libfreetype6-dev \
        libicu-dev \
        libjpeg-dev \
        libmagickwand-dev \
        libmagickcore-6.q16-6-extra \
        libpng-dev \
        libwebp-dev \
        libzip-dev; \
    rm -rf /var/lib/apt/lists/*

# Install and enable PHP extensions
RUN set -eux; \
    docker-php-ext-configure gd \
        --with-avif \
        --with-freetype \
        --with-jpeg \
        --with-webp; \
    docker-php-ext-install -j"$(nproc)" \
        gd \
        bcmath \
        exif \
        intl \
        mysqli \
        zip; \
    pecl install imagick && docker-php-ext-enable imagick; \
    php -m | grep -E 'imagick|zip|gd'

# Enable Apache modules
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

# Download and set up ClassicPress
RUN set -eux; \
    curl -o classicpress.tar.gz -fL "https://github.com/ClassicPress/ClassicPress-release/archive/refs/tags/2.3.1.tar.gz"; \
    tar -xzf classicpress.tar.gz -C /usr/src/; \
    mv /usr/src/ClassicPress-release-2.3.1 /usr/src/classicpress; \
    rm classicpress.tar.gz; \
    chown -R www-data:www-data /usr/src/classicpress

# Prepare wp-content (or equivalent)
RUN set -eux; \
    mkdir -p /usr/src/classicpress/wp-content; \
    chown -R www-data:www-data /usr/src/classicpress/wp-content; \
    chmod -R 1777 /usr/src/classicpress/wp-content

# Define the volume for user content
VOLUME /var/www/html

# Copy entrypoint and configuration files
COPY --chown=www-data:www-data php8.3/wp-config-docker.php /usr/src/classicpress/
COPY php8.3/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set the entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
