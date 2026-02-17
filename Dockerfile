# Dockerfile
FROM php:8.2-cli

WORKDIR /app

# Installer les extensions et dépendances
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier le code de l'application
COPY . /app

# Installer les dépendances PHP
RUN composer install --no-dev --no-interaction --optimize-autoloader || true

# Exposer le port 8000 pour le serveur PHP intégré
EXPOSE 8000

# Lancer le serveur PHP intégré
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]

