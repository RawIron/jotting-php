FROM php:5.6-alpine

#---> SYSTEM <---

# install php
#RUN apk update && \
#    apk add php php-json php-phar php-openssl

# install composer so it handles the php package installation
RUN apk update && \
    apk add curl git

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer


#---> DEPLOY <---

# create the app directory
RUN mkdir -p /var/www/app

# php packages
COPY composer.json /var/www/app/composer.json
RUN cd /var/www/app && \
    composer install

# configure phpunit
COPY phpunit.xml /var/www/app/phpunit.xml

# deploy source

# run tests
RUN cd /var/www/app && \
    phpunit
