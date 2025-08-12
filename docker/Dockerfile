FROM php:7.2-apache
 
# Fix APT sources for archived Debian Stretch
RUN sed -i '/stretch-updates/d' /etc/apt/sources.list \
    && sed -i 's|http://deb.debian.org/debian|http://archive.debian.org/debian|g' /etc/apt/sources.list \
    && sed -i 's|http://security.debian.org/debian-security|http://archive.debian.org/debian-security|g' /etc/apt/sources.list \
    && echo 'Acquire::Check-Valid-Until "false";' > /etc/apt/apt.conf.d/99no-check-valid-until
 
# Install required PHP extensions
RUN apt-get update && apt-get install -y --allow-unauthenticated  \
    libpng-dev \
    libjpeg-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    locales \
    && docker-php-ext-install pdo pdo_mysql mbstring xml zip
 
# Install Composer (compatible with PHP 7.0)
COPY --from=composer:1 /usr/bin/composer /usr/bin/composer
# Install Oil CLI globally
RUN curl -L https://get.fuelphp.com/oil | sh
 
# Set working directory
WORKDIR /var/www/html 
 
COPY composer.json /var/www/html/
# COPY composer.lock /var/www/html/
 
RUN composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader 
 
# Apache configuration
COPY webserver/apache2.conf /etc/apache2/apache2.conf
COPY webserver/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY webserver/fuelapp.htaccess /var/www/html/fuel/.htaccess

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
 
RUN a2enmod rewrite
EXPOSE 80
 
# Locale and timezone
RUN echo "ja_JP.UTF-8 UTF-8" >> /etc/locale.gen && locale-gen ja_JP.UTF-8
ENV LANG=ja_JP.UTF-8 \
    LANGUAGE=ja_JP:ja \
    LC_ALL=ja_JP.UTF-8
# RUN ln -sf /usr/share/zoneinfo/Asia/Tokyo /etc/localtime && echo "Asia/Tokyo" > /etc/timezone
 
RUN docker-php-ext-install mysqli

CMD ["apache2-foreground"]