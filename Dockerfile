FROM php:zts-alpine

RUN apk update && apk add --no-cache \
    sudo bash \
    g++ make autoconf

RUN curl -sSL https://github.com/krakjoe/pthreads/archive/master.zip -o /tmp/pthreads.zip \
    && unzip /tmp/pthreads.zip -d /tmp \
    && cd /tmp/pthreads-* \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && rm -rf /tmp/pthreads*

RUN docker-php-ext-enable pthreads
