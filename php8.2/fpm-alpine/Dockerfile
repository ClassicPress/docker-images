#
# ClassicPress on PHP 8.2, PHP-FPM, Alpine Linux.
# Based on the official WordPress fpm-alpine image (docker-library/wordpress).
#
# This image runs PHP-FPM only (port 9000). Put a web server such as nginx in
# front of it; see docker-compose.fpm-alpine.yml and nginx/default.conf.
#

FROM php:8.2-fpm-alpine

# persistent dependencies
RUN set -eux; \
	apk add --no-cache \
# in theory, docker-entrypoint.sh is POSIX-compliant, but priority is a working, consistent image
		bash \
# Ghostscript is required for rendering PDF previews
		ghostscript \
# Alpine package for "imagemagick" contains ~120 .so files, see: https://github.com/docker-library/wordpress/pull/497
		imagemagick \
# Alpine packages ImageMagick's coders separately; without this Imagick reports no AVIF
# support and WordPress silently falls back to GD, which discards ICC profiles
		imagemagick-heic \
	;

# install the PHP extensions we need (https://make.wordpress.org/hosting/handbook/handbook/server-environment/#php-extensions)
RUN set -ex; \
	\
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		freetype-dev \
		icu-dev \
		imagemagick-dev libheif-dev \
		libavif-dev \
		libjpeg-turbo-dev \
		libpng-dev \
		libwebp-dev \
		libzip-dev \
	; \
	\
	docker-php-ext-configure gd \
		--with-avif \
		--with-freetype \
		--with-jpeg \
		--with-webp \
	; \
	docker-php-ext-install -j "$(nproc)" \
		bcmath \
		exif \
		gd \
		intl \
		mysqli \
		zip \
	; \
# WARNING: imagick is likely not supported on Alpine: https://github.com/Imagick/imagick/issues/328
# https://pecl.php.net/package/imagick
	n=0; until pecl install imagick-3.8.1; do n=$((n+1)); [ "$n" -ge 8 ] && exit 1; echo "pecl install failed, retry $n"; sleep 15; done; \
	docker-php-ext-enable imagick; \
	rm -r /tmp/pear; \
	\
# some misbehaving extensions end up outputting to stdout 🙈 (https://github.com/docker-library/wordpress/issues/669#issuecomment-993945967)
	out="$(php -r 'exit(0);')"; \
	[ -z "$out" ]; \
	err="$(php -r 'exit(0);' 3>&1 1>&2 2>&3)"; \
	[ -z "$err" ]; \
	\
	extDir="$(php -r 'echo ini_get("extension_dir");')"; \
	[ -d "$extDir" ]; \
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive "$extDir" \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-network --virtual .wordpress-phpexts-rundeps $runDeps; \
	apk del --no-network .build-deps; \
	\
	! { ldd "$extDir"/*.so | grep 'not found'; }; \
# check for output like "PHP Warning:  PHP Startup: Unable to load dynamic library 'foo' (tried: ...)
	err="$(php --version 3>&1 1>&2 2>&3)"; \
	[ -z "$err" ]

# Install WP-CLI (pinned and checksum-verified; see https://github.com/wp-cli/wp-cli/releases)
RUN set -eux; \
    wpCliVersion='2.12.0'; \
    wpCliSha512='be928f6b8ca1e8dfb9d2f4b75a13aa4aee0896f8a9a0a1c45cd5d2c98605e6172e6d014dda2e27f88c98befc16c040cbb2bd1bfa121510ea5cdf5f6a30fe8832'; \
    curl -fsSL --retry 5 --retry-all-errors --retry-delay 10 -o /usr/local/bin/wp "https://github.com/wp-cli/wp-cli/releases/download/v$wpCliVersion/wp-cli-$wpCliVersion.phar"; \
    echo "$wpCliSha512 */usr/local/bin/wp" | sha512sum -c -; \
    chmod +x /usr/local/bin/wp; \
    wp --allow-root --info

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
# enabled by default in the php images: https://github.com/docker-library/php/pull/1587
RUN set -eux; \
	{ \
		echo 'opcache.memory_consumption=128'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.max_accelerated_files=4000'; \
		echo 'opcache.revalidate_freq=2'; \
	} > "$PHP_INI_DIR/conf.d/opcache-recommended.ini"
# https://wordpress.org/support/article/editing-wp-config-php/#configure-error-logging
RUN set -eux; \
	{ \
# https://www.php.net/manual/en/errorfunc.constants.php
# https://github.com/docker-library/wordpress/issues/420#issuecomment-517839670
		echo 'error_reporting = E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_RECOVERABLE_ERROR'; \
		echo 'display_errors = Off'; \
		echo 'display_startup_errors = Off'; \
		echo 'log_errors = On'; \
		echo 'error_log = /dev/stderr'; \
		echo 'log_errors_max_len = 1024'; \
		echo 'ignore_repeated_errors = On'; \
		echo 'ignore_repeated_source = Off'; \
		echo 'html_errors = Off'; \
	} > "$PHP_INI_DIR/conf.d/error-logging.ini"

RUN set -eux; \
    version='2.7.4'; \
    sha256='59a8b384645b4c5c17561294531679561874d4603dce40a7a2c721603338bdf3'; \
    \
    curl -o classicpress.tar.gz -fL --retry 5 --retry-all-errors --retry-delay 10 "https://github.com/ClassicPress/ClassicPress-release/archive/refs/tags/$version.tar.gz"; \
    echo "$sha256 *classicpress.tar.gz" | sha256sum -c -; \
    \
    # Extract ClassicPress tarball
    tar -xzf classicpress.tar.gz -C /usr/src/; \
    mv /usr/src/ClassicPress-release-$version /usr/src/classicpress; \
    rm classicpress.tar.gz; \
    \
    # Create default .htaccess for ClassicPress
    [ ! -e /usr/src/classicpress/.htaccess ]; \
    { \
        echo '# BEGIN ClassicPress'; \
        echo ''; \
        echo 'RewriteEngine On'; \
        echo 'RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]'; \
        echo 'RewriteBase /'; \
        echo 'RewriteRule ^index\.php$ - [L]'; \
        echo 'RewriteCond %{REQUEST_FILENAME} !-f'; \
        echo 'RewriteCond %{REQUEST_FILENAME} !-d'; \
        echo 'RewriteRule . /index.php [L]'; \
        echo ''; \
        echo '# END ClassicPress'; \
    } > /usr/src/classicpress/.htaccess; \
    \
    # Set correct permissions
    chown -R www-data:www-data /usr/src/classicpress; \
    \
    # Pre-create wp-content (or equivalent) directories
    mkdir wp-content; \
    for dir in /usr/src/classicpress/wp-content/*/ cache; do \
        dir="$(basename "${dir%/}")"; \
        mkdir "wp-content/$dir"; \
    done; \
    chown -R www-data:www-data wp-content; \
    chmod -R 1777 wp-content

VOLUME /var/www/html

COPY --chown=www-data:www-data php8.2/wp-config-docker.php /usr/src/classicpress/
COPY php8.2/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
