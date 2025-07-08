# Menggunakan image PHP dengan Apache
FROM php:8.2-apache

# Install dependensi yang dibutuhkan untuk Laravel dan Composer
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Set document root ke folder public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Menyalin seluruh aplikasi Laravel ke dalam container
COPY . /var/www/html/

# Mengatur izin agar Apache bisa menulis ke direktori yang dibutuhkan oleh Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Install dependensi Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Membersihkan cache untuk mengurangi ukuran image
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Mengekspos port 80 untuk akses aplikasi
EXPOSE 80

# Menjalankan Apache di foreground
CMD ["apache2-foreground"]
