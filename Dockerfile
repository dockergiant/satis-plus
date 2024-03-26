FROM php:8-cli-alpine

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN set -eux ; \
  apk upgrade --no-cache ; \
  apk add --no-cache --upgrade \
    bash \
    curl \
    git \
    mercurial \
    openssh \
    openssl \
    p7zip \
    subversion \
    unzip \
    zip ; \
  install-php-extensions \
    bz2 \
    sockets \
    zip

ENV COMPOSER_HOME /composer

COPY php-cli.ini /usr/local/etc/php/
COPY . /satis/

WORKDIR /build

ENTRYPOINT ["/satis/bin/docker-entrypoint.sh"]

CMD ["--ansi", "-vvv", "build", "/build/satis.json", "/build/output"]