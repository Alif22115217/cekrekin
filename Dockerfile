# Menggunakan image PHP resmi dengan Apache sebagai base image
FROM php:8.2-apache

# Install dependensi untuk PHP dan ekstensi yang diperlukan
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Menyalin file aplikasi ke dalam container
COPY . /var/www/html/

# Menyeting direktori dokumen root ke folder public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Menginstall Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Mengatur izin agar Apache dapat menulis ke direktori yang dibutuhkan
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Install dependensi PHP Laravel menggunakan Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Membersihkan cache untuk mengurangi ukuran image
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Mengekspos port 80 untuk aplikasi
EXPOSE 80

# Menjalankan Apache di background
CMD ["apache2-foreground"]
