# Gunakan image PHP dengan Apache
FROM php:8.2-apache

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# Set DocumentRoot ke public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Salin file aplikasi ke dalam container
COPY . /var/www/html/

# Install dependensi aplikasi Laravel
RUN composer install --no-dev --optimize-autoloader

# Ekspose port 80
EXPOSE 80

# Jalankan Apache di background
CMD ["apache2-foreground"]
