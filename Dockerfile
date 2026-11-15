# Imagen oficial de PHP 8.2 con servidor Apache incluido
FROM php:8.2-apache

# Instalar las extensiones necesarias para conectarse a MySQL/MariaDB mediante PDO
RUN docker-php-ext-install pdo pdo_mysql
