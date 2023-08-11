FROM php:8.2-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
    zlib1g-dev \
    libpng-dev \
    libxslt-dev \
    libpq-dev \
    libmemcached-dev \
    libmagickwand-dev \
    libgmp-dev \
    curl \
    git \
    wget \
    unzip \
    nodejs \
    npm \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libzip-dev \
    libssl-dev \
    acl

RUN wget https://getcomposer.org/installer -O - -q \
    | php -- --install-dir=/bin --filename=composer --quiet

RUN pecl install xdebug-3.2.1
RUN docker-php-ext-enable xdebug

RUN docker-php-ext-install mysqli pdo_mysql pcntl bcmath soap intl exif gmp xsl sockets gd zip

ADD ./php.ini /usr/local/etc/php/php.ini

RUN echo "memory_limit=512M" >> /usr/local/etc/php/php.ini
RUN echo "opcache.memory_consumption=256M" >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.mode=debug' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.discover_client_host=true' >> /usr/local/etc/php/php.ini

COPY /vhosts/default-vhost.conf /etc/apache2/sites-available/
RUN a2dissite 000-default.conf
RUN a2ensite default-vhost.conf
RUN a2enmod rewrite

EXPOSE 80

WORKDIR /var/www/html