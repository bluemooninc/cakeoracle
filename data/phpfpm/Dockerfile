ARG     PHP_VERSION=7.2

FROM	php:${PHP_VERSION}-fpm-alpine

LABEL	maintainer="https://github.com/hermsi1337"

ENV     PHPREDIS_VERSION="4.0.2"

ADD     http://downloads3.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz /tmp/
ADD     https://github.com/phpredis/phpredis/archive/${PHPREDIS_VERSION}.tar.gz /tmp/

RUN     apk update                       && \
        \
        apk upgrade                      && \
        \
        docker-php-source extract        && \
        \
        apk add --no-cache                  \
            --virtual .build-dependencies   \
                $PHPIZE_DEPS                \
                zlib-dev                    \
                cyrus-sasl-dev              \
                git                         \
                autoconf                    \
                g++                         \
                libtool                     \
                make                        \
                pcre-dev                 && \
        \
        apk add --no-cache                  \
            tini                            \
            libintl                         \
            icu                             \
            icu-dev                         \
            libxml2-dev                     \
            freetype-dev                    \
            libjpeg-turbo-dev               \
            libpng-dev                      \
            gmp                             \
            gmp-dev                         \
            libmemcached-dev                \
            imagemagick-dev                 \
            libssh2                         \
            libssh2-dev                     \
            libxslt-dev
## For MySQL and basic setting
RUN set -x && \
  apk add --no-cache icu-libs && \
  apk add --no-cache --virtual build-dependencies && \
  NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
  docker-php-ext-install -j${NPROC} intl && \
  docker-php-ext-install -j${NPROC} mysqli && \
  docker-php-ext-install -j${NPROC} pdo_mysql && \
  docker-php-ext-install -j${NPROC} pdo && \
  docker-php-ext-install -j${NPROC} mbstring && \
  apk del --no-cache --purge build-dependencies && \
  rm -rf /tmp/pear
## For ZIP and SSH
RUN wget "https://github.com/Sean-Der/pecl-networking-ssh2/archive/php7.zip" \
    && unzip php7.zip \
    && cd pecl-networking-ssh2-php7 \
    && phpize \
    && ./configure \
    && make && make install \
    && docker-php-ext-enable ssh2
## TODO: For ORACLE OCI libraly
#RUN \
#  wget -qO- https://github.com/srebrinb/Docker-Apache-php7-oci8/raw/master/instantclient-basic-linux.x64-12.1.0.2.0.zip && \
#  unzip instantclient-basic-linux.x64-12.1.0.2.0.zip -d /usr/local && \
#  wget -qO- https://github.com/srebrinb/Docker-Apache-php7-oci8/raw/master/instantclient-sdk-linux.x64-12.1.0.2.0.zip && \
#  unzip instantclient-sdk-linux.x64-12.1.0.2.0.zip -d /usr/local && \
#  wget -qO- https://github.com/srebrinb/Docker-Apache-php7-oci8/raw/master/instantclient-sqlplus-linux.x64-12.1.0.2.0.zip && \
#  unzip instantclient-sqlplus-linux.x64-12.1.0.2.0.zip -d /usr/local
#RUN \
#  ln -s /usr/local/instantclient_12_2 /usr/local/instantclient && \
#  ln -s /usr/local/instantclient/libclntsh.so.* /usr/local/instantclient/libclntsh.so && \
#  ln -s /usr/local/instantclient/lib* /usr/lib && \
#  ln -s /usr/local/instantclient/sqlplus /usr/bin/sqlplus
#RUN \
#  docker-php-ext-configure oci8 --with-oci8=instantclient,/usr/local/instantclient && \
#  docker-php-ext-install oci8 && \
#  rm -rf /var/lib/apt/lists/*
RUN set -x && \
  deluser www-data && \
  addgroup -g 50 -S www-data && \
  adduser -u 1000 -D -S -G www-data www-data
VOLUME /etc/tnsnames.ora