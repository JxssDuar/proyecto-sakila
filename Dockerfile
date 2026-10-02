FROM php:8.2-apache

# Dejar solamente el MPM compatible con mod_php
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_worker.load \
          /etc/apache2/mods-enabled/mpm_worker.conf \
    && a2enmod mpm_prefork

# Extensión necesaria para conectarse a MySQL
RUN docker-php-ext-install pdo_mysql

# Copiar el proyecto
COPY . /var/www/html/

# Permisos
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]