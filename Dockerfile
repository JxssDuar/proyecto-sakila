FROM php:8.2-apache

# Deshabilitar los MPM incompatibles antes de habilitar el de mod_php.
RUN a2dismod -f mpm_event mpm_worker; \
    rm -f /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_worker.load \
          /etc/apache2/mods-enabled/mpm_worker.conf; \
    a2enmod mpm_prefork

# Conservar PDO MySQL y mysqli para la conexión central de las páginas.
RUN docker-php-ext-install pdo_mysql mysqli

# Copiar el proyecto
COPY . /var/www/html/

# Corregir los MPM en cada arranque, aunque otro comando de Railway los reactive.
COPY docker-start.sh /usr/local/bin/railway-start
RUN chmod +x /usr/local/bin/railway-start

# Permisos
RUN chown -R www-data:www-data /var/www/html

# No publicar una imagen con Apache inválido, varios MPM o sin MySQL.
RUN set -eu; \
    apache2ctl configtest; \
    modules="$(apache2ctl -M)"; \
    printf '%s\n' "$modules"; \
    test "$(printf '%s\n' "$modules" | grep -c 'mpm_.*_module')" -eq 1; \
    printf '%s\n' "$modules" | grep -q 'mpm_prefork_module'; \
    php --ri pdo_mysql; \
    php --ri mysqli; \
    php -r 'exit(extension_loaded("pdo_mysql") && in_array("mysql", PDO::getAvailableDrivers(), true) && extension_loaded("mysqli") ? 0 : 1);'; \
    echo 'Railway-Sakila: root Dockerfile validated'

EXPOSE 80

CMD ["/usr/local/bin/railway-start"]
