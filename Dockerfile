FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# System deps + ImageMagick dev headers + Node.js dependencies
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        netcat-openbsd \
        build-essential \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        locales \
        zip \
        jpegoptim optipng pngquant gifsicle \
        vim unzip git curl \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        imagemagick \
        librsvg2-2 \
        librsvg2-bin \
        libmagickwand-dev \
        nodejs \
        npm && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip && \
    pecl install imagick && \
    docker-php-ext-enable imagick && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code
COPY . /var/www

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --no-progress

# Install Node dependencies & build assets
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www

# Expose port
EXPOSE 9000

CMD ["php-fpm"]