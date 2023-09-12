FROM php:8.2-fpm

WORKDIR /var/www/html

ARG user
ARG uid

RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user && chown -R $user:$uid /home/$user && chown -R $user:$uid /var/www/html//

COPY --chown=$user:$uid . /var/www/html/

RUN apt-get update
RUN apt-get install -y \
    git \
    zip \
    curl \
    sudo \
    unzip \
    libicu-dev \
    libbz2-dev \
    libpng-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libreadline-dev \
    libfreetype6-dev \
    g++ \
    nano \
    cron

#RUN pecl install xdebug
#RUN cd $PHP_INI_DIR/conf.d && printf "zend_extension=xdebug;\nxdebug.mode=coverage;" > 99-xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/bin/composer

RUN docker-php-ext-install pdo pdo_mysql exif

USER $user
CMD ["php-fpm"]