FROM php:8.2-apache

# MySQL Extension Install
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Apache Rewrite Enable
RUN a2enmod rewrite

# Website Files Copy
COPY . /var/www/html/

# Permission
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]