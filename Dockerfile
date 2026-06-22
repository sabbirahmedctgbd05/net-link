FROM php:8.1-apache

# প্রয়োজনীয় এক্সটেনশন ইন্সটল করা
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Apache rewrite module চালু করা
RUN a2enmod rewrite

# ফাইল কপি করা
COPY . /var/www/html/

# পারমিশন সেট করা (সুরক্ষার জন্য)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80