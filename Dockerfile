# Dockerfile
FROM php:8.2-cli

WORKDIR /app

# Installer PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copier le code de l'application
COPY . /app

# Exposer le port 8000 pour le serveur PHP intégré
EXPOSE 8000

# Lancer le serveur PHP intégré
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
