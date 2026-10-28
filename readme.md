# 🐳 Welcome to the Official Docker Image for ClassicPress
This is the official Docker image for ClassicPress — a lightweight, secure, and extendable content management system (CMS) that preserves the classic editing experience of WordPress 4.9.x.

This image provides a streamlined, production-ready container for running ClassicPress with minimal setup. It supports both Apache and PHP-FPM configurations and is ideal for development, testing, or deployment in Docker-based environments.

✨ Features
* Based on official images (Apache, and PHP-FPM on Alpine Linux)
* Support volume mounting for perisistent content
* Simple environment-variable-based configuration
* Compatiible with Docker Compose
* Actively maintained by the ClassicPress community

## Supported Tags

- [2.7.4‑php8.1‑apache](https://hub.docker.com/r/classicpress/classicpress/tags?page=1&name=php8.1-apache)  
- [2.7.4‑php8.2‑apache](https://hub.docker.com/r/classicpress/classicpress/tags?page=1&name=php8.2-apache)  
- [2.7.4‑php8.3‑apache](https://hub.docker.com/r/classicpress/classicpress/tags?page=1&name=php8.3-apache)  
- [2.7.4‑php8.4‑apache](https://hub.docker.com/r/classicpress/classicpress/tags?page=1&name=php8.4-apache)
- [2.7.4‑php8.5‑apache](https://hub.docker.com/r/classicpress/classicpress/tags?page=1&name=php8.5-apache)

### PHP-FPM on Alpine Linux

Smaller images that run PHP-FPM only (port 9000), with no web server inside. Put nginx or another web server in front; see `docker-compose.fpm-alpine.yml` and `nginx/default.conf` for a working example.

- [2.7.4‑php8.2‑fpm‑alpine](https://hub.docker.com/r/classicpress/classicpress/tags?page=1&name=php8.2-fpm-alpine)
- [2.7.4‑php8.3‑fpm‑alpine](https://hub.docker.com/r/classicpress/classicpress/tags?page=1&name=php8.3-fpm-alpine)
- [2.7.4‑php8.4‑fpm‑alpine](https://hub.docker.com/r/classicpress/classicpress/tags?page=1&name=php8.4-fpm-alpine)
- [2.7.4‑php8.5‑fpm‑alpine](https://hub.docker.com/r/classicpress/classicpress/tags?page=1&name=php8.5-fpm-alpine) (also `fpm-alpine`)

```
docker compose -f docker-compose.fpm-alpine.yml up -d
```

## WP-CLI

Every image includes [WP-CLI](https://wp-cli.org/) at `/usr/local/bin/wp`, so you don't need a separate CLI container. Run it as `www-data`, so new files get the same owner as the site:

```
docker exec -u www-data <container> wp plugin list
```

Don't use `wp core update` or `wp core download`. They install WordPress, not ClassicPress. To update ClassicPress, pull a newer image or use the updater in the dashboard.

## Supported Architectures

- `amd64`  
- `arm64`
