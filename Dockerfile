FROM registry.songhuwan.com/library/hyperf-base:7.4
MAINTAINER ilovinti <ilovintit@gmail.com>

RUN apt-get update -y && apt-get install apache2 libapache2-mod-php7.4 -y

WORKDIR /opt/www

# Composer Cache
COPY ./composer.* /opt/www/
RUN composer install --no-dev --no-scripts

COPY . /opt/www
RUN composer install --no-dev -o

COPY httpd-foreground /usr/local/bin
RUN chmod +x /usr/local/bin/httpd-foreground && chown -R www-data:www-data /opt/www

RUN rm -rf /var/www/html && ln -s /opt/www/public /var/www/html

RUN { \
    echo '<VirtualHost *:80>';\
    	echo 'ServerAdmin webmaster@localhost';\
    	echo 'DocumentRoot /var/www/html';\
    	echo 'ErrorLog ${APACHE_LOG_DIR}/error.log';\
    	echo 'CustomLog ${APACHE_LOG_DIR}/access.log combined';\
    echo '</VirtualHost>';\
} > /etc/apache2/sites-available/000-default.conf


ENTRYPOINT ["/usr/local/bin/httpd-foreground"]
