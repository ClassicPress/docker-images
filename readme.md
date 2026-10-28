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

Each image is published under three tags, so you can choose how closely to pin it:

| Tag | Example | Gets |
|---|---|---|
| `php<version>-<variant>` | `php8.4-apache` | Every new ClassicPress release |
| `<major.minor>-php<version>-<variant>` | `2.7-php8.4-apache` | Bug-fix releases of ClassicPress 2.7 only |
| `<full version>-php<version>-<variant>` | `2.7.4-php8.4-apache` | Exactly ClassicPress 2.7.4 |

Tags for the current ClassicPress release are rebuilt weekly, so they also pick up PHP and operating system security fixes. Tags for older releases stop being updated. `latest` is `php8.5-apache`.

### Apache

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

Every image includes [WP-CLI](https://wp-cli.org/) at `/usr/local/bin/wp`. Run it as `www-data`, so new files get the same owner as the site:

```
docker exec -u www-data <container> wp plugin list
```

## Supported Architectures

- `amd64`  
- `arm64`

## Maintainers: updating ClassicPress

Run the update script with the new version, review the diff, and commit:

```
./update-classicpress.sh 2.7.5
git diff
```

It downloads the release, pins its SHA-256 checksum in every Dockerfile, and updates the version in this readme. Builds fail if the downloaded file doesn't match the checksum.

Pull requests and pushes to `develop` build and test every image without publishing. Pushes to `main` and the weekly scheduled run publish to Docker Hub.
