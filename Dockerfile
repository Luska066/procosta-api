FROM php:8.3-fpm

# Definir diretório de trabalho
WORKDIR /var/www/html

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    zip \
    curl \
    unzip \
    git \
    wget \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP necessárias para Laravel
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    ctype \
    curl \
    dom \
    fileinfo \
    filter \
    iconv

# Instalar GD (para processamento de imagens)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Instalar Redis via PECL
RUN pecl install redis && docker-php-ext-enable redis || true

# Copiar arquivo de configuração PHP
COPY public/php.ini /usr/local/etc/php/conf.d/docker-php-config.ini

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar arquivos do projeto
COPY . .

RUN rm -rf vendor composer.lock node_modules package-lock.json

# Definir permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

    
# NVM e Node.js
ENV NVM_DIR /root/.nvm
ENV NODE_VERSION 22

RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest \
    && npm install \
    && npm run build \
    && composer install \
    && php artisan key:generate \
    && php artisan migrate

# Expor porta 9000 (FPM)
EXPOSE 9000

# Comando inicial
CMD ["php-fpm"]
