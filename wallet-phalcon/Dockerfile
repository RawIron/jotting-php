FROM alpine

#---> SYSTEM <---

# install php
RUN apk update && \
    apk add php php-openssl php-dom php-phalcon

# install composer so it handles the php package installation
RUN echo "http://dl-3.alpinelinux.org/alpine/edge/testing" >>/etc/apk/repositories && \
    apk update && \
    apk add git composer


#---> DEPLOY <---

# create the app directory
RUN mkdir -p /var/www/app/src

WORKDIR /var/www/app

# php packages
COPY composer.json composer.json
RUN composer install

# deploy source
COPY src src

# configure phpunit and run tests
COPY phpunit.xml phpunit.xml
RUN vendor/bin/phpunit
