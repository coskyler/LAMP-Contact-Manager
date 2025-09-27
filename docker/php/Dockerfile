FROM php:8.2-apache
RUN docker-php-ext-install pdo_mysql

RUN a2enmod rewrite
#enable .htaccess for apache
RUN echo '<Directory /var/www/html>\n    AllowOverride All\n</Directory>' > /etc/apache2/conf-available/allow-override.conf
RUN a2enconf allow-override