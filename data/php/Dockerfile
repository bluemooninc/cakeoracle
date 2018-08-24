#
# Apache + PHP
#
# 2017-01-15
#   CentOS 7.3 + epel,remi
#   Apache 2.4.6

FROM php:7.2-apache
MAINTAINER y.sakai

RUN apt-get update &&\
    apt-get install apt-utils unzip -y &&\
    apt-get install libaio1 libaio-dev -y &&\
    apt-get install libicu-dev -y

# for MySQL PHP extention
RUN docker-php-ext-configure intl \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli

# Install Oracle Instantclient
RUN mkdir -p /opt/oracle
COPY instantclient-basic-linux.x64-12.1.0.2.0.zip /opt/oracle/instantclient-basic.zip
COPY instantclient-sdk-linux.x64-12.1.0.2.0.zip /opt/oracle/instantclient-sdk.zip
RUN cd /opt/oracle \
    && unzip /opt/oracle/instantclient-basic.zip -d /opt/oracle \
    && unzip /opt/oracle/instantclient-sdk.zip -d /opt/oracle \
    && ln -s /opt/oracle/instantclient_12_1 /opt/oracle/instantclient \
    && ln -s /opt/oracle/instantclient_12_1/libclntsh.so.12.1 /opt/oracle/instantclient/libclntsh.so \
    && ln -s /opt/oracle/instantclient_12_1/libclntshcore.so.12.1 /opt/oracle/instantclient/libclntshcore.so \
    && ln -s /opt/oracle/instantclient_12_1/libocci.so.12.1 /opt/oracle/instantclient/libocci.so \
    && rm -rf /opt/oracle/*.zip

RUN echo "extension=oci8.so" >> /usr/local/etc/php/php.ini
RUN echo "extension=pdo_oci.so" >> /usr/local/etc/php/php.ini

# Install Oracle extensions
RUN docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/opt/oracle/instantclient_12_1,12.1 \
    && docker-php-ext-install \
        pdo_oci \
    && echo 'instantclient,/opt/oracle/instantclient_12_1/' | pecl install oci8 \
    && docker-php-ext-enable \
        oci8

# Install the gmp extensions
RUN apt-get update -y
RUN apt-get install -y libgmp-dev re2c file
RUN ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/local/include/
RUN docker-php-ext-configure gmp
RUN docker-php-ext-install gmp

RUN ldconfig

# Install composer
RUN curl -s http://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

RUN apt-get update && \
    apt-get install -y libicu-dev && \
    docker-php-ext-install intl

# timezone
RUN cp -p /usr/share/zoneinfo/Japan /etc/localtime

ADD ./httpd.conf /etc/httpd/conf/httpd.conf

EXPOSE 80
