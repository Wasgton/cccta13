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
    cron \
    wget

RUN wget -c "https://xdebug.org/files/xdebug-3.2.0.tgz"
RUN tar -xf xdebug-3.2.0.tgz
RUN cd xdebug-3.2.0 && phpize && ./configure && make && make install
RUN echo "zend_extension=xdebug.so" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=develop" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_port=9000" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.discover_client_host=0" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.log_level=3" >> /usr/local/etc/php/conf.d/xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/bin/composer

RUN docker-php-ext-install pdo pdo_mysql exif

USER $user
CMD ["php-fpm"]