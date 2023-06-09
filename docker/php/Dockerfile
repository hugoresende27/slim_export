#FROM php:8.1-fpm
#
## Rest of your Dockerfile instructions
#
#RUN apt update \
#    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip libpq-dev \
#    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
#    && docker-php-ext-install intl opcache pdo pdo_pgsql \
#    && pecl install apcu \
#    && docker-php-ext-enable apcu \
#    && docker-php-ext-configure zip \
#    && docker-php-ext-install zip
#
#WORKDIR /var/www/slim_app
#COPY . /var/www/slim_app
#
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
#
#RUN git config --global user.email "you@example.com" \
#    && git config --global user.name "Your Name"
#
#RUN docker-php-ext-install sockets
#
#RUN docker-php-ext-install pdo pdo_mysql
#
## Set the file permissions for the working directory
#RUN chown -R www-data:www-data /var/www/slim_app \
#    && chmod -R 755 /var/www/slim_app

FROM php:8.1-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev libpq-dev libpng-dev libjpeg-dev libfreetype6-dev  \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install intl opcache pdo pdo_pgsql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd \
    && docker-php-ext-install sockets \
    && docker-php-ext-install pdo pdo_mysql

# Install MongoDB extension
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Set the working directory
WORKDIR /var/www/slim_app

# Copy project files
COPY . /var/www/slim_app

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install project dependencies
RUN #composer install --no-dev --optimize-autoloader

# Set file permissions for the working directory
RUN chown -R www-data:www-data /var/www/slim_app \
    && chmod -R 755 /var/www/slim_app

# Set git configuration
RUN git config --global user.email "you@example.com" \
    && git config --global user.name "Your Name"




