# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG PHP_VERSION=8.1
ARG CADDY_VERSION=2

# "php" stage
FROM gitlab-r.core.webant.ru/devops/api-platform-php-fpm-alpine:${PHP_VERSION} AS api_platform_php


# build for production
ARG APP_ENV=prod

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.json  symfony.lock auth.json ./

RUN set -eux; \
	composer install -vvv --prefer-dist --no-dev --no-scripts --no-progress; \
	composer clear-cache

# copy only specifically what we need
COPY .env ./
COPY bin bin/
COPY config config/
COPY migrations migrations/
COPY public public/
COPY src src/
COPY templates templates/
COPY translations translations/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync
VOLUME /srv/api/var

ENV SYMFONY_PHPUNIT_VERSION=9


# "caddy" stage
# depends on the "php" stage above
FROM caddy:${CADDY_VERSION}-builder-alpine AS api_platform_caddy_builder

RUN apk add nss

RUN xcaddy build \
    --with github.com/dunglas/mercure/caddy \
    --with github.com/dunglas/vulcain/caddy

FROM caddy:${CADDY_VERSION} AS api_platform_caddy

WORKDIR /srv/api

COPY --from=dunglas/mercure:v0.11 /srv/public /srv/mercure-assets/
COPY --from=api_platform_caddy_builder /usr/bin/caddy /usr/bin/caddy
COPY --from=api_platform_php /srv/api/public public/
COPY docker/caddy/Caddyfile /etc/caddy/Caddyfile
