# ==========================================
# STAGE 1: Build Frontend Assets (Vite)
# ==========================================
FROM node:20-alpine AS asset-builder

WORKDIR /app

# Salin package files secara eksplisit untuk menghindari skip dari .dockerignore
COPY package*.json ./

# Install SEMUA dependensi termasuk Vite
RUN npm ci

# Salin sisa file yang dibutuhkan untuk build (resources, vite.config.js, dll)
COPY . .

# Jalankan kompilasi Vite
RUN npm run build

# ==========================================
# STAGE 2: Production Image (FrankenPHP)
# ==========================================
FROM dunglas/frankenphp:1-php8.3-alpine

# Install ekstensi PHP yang umum dibutuhkan Laravel
RUN apk add --no-cache \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip gd intl pcntl

# Install Composer resmi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy seluruh source code Laravel dari host
COPY . .

# AMBIL HASIL BUILD DARI STAGE 1 (Menimpa folder public/build dengan versi terkompilasi)
COPY --from=asset-builder /app/public/build ./public/build

# Bersihkan file development/repository yang tidak dibutuhkan di production
RUN rm -rf .git .github README.md CONTRIBUTING.md .claude PRD.md .vscode .dockerignore Dockerfile node_modules

# Install dependensi Laravel (tanpa dev-dependencies)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Atur izin folder agar FrankenPHP bisa menulis cache & log
RUN chown -R root:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Copy custom Caddyfile (non-HTTPS untuk internal/dev)
COPY Caddyfile /etc/caddy/Caddyfile

# Expose port HTTP default FrankenPHP
EXPOSE 80

# Jalankan FrankenPHP dengan root direktori publik Laravel
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]