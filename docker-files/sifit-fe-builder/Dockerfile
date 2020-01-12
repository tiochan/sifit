FROM php:7.2-apache
#FROM php:7.2-fpm

MAINTAINER Sebas <sebastian.gomez@adevinta.com>

RUN apt-get update &&\
    apt-get install --no-install-recommends --assume-yes --quiet \
    libpng-dev ca-certificates cron

WORKDIR /var/www/html/
RUN docker-php-ext-install mysqli gd

ADD src /var/www/html/
# RUN cp /var/www/html/doc/sifit_cron /etc/cron.d/
# ADD --chown=www-data:www-data sifit/ /var/www/html/sifit/
EXPOSE 80

##
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
##

#ENV MYSQL_END_POINT="sifit-db.cre-dev.mpi-internal.com"
ENV MYSQL_END_POINT="127.0.0.1"
ENV MYSQL_ROOT_PASSWORD=root
ENV MYSQL_ROOT_USER=root

# RUN sed -i 's#"HOME","/sifit"#"HOME",""#g' /var/www/html/conf/app.conf.php
# RUN sed -i "s#localhost#${MYSQL_END_POINT}#g" /var/www/html/conf/app.conf.php
# #RUN sed -i "s#DEVELOPMENT\",true#DEVELOPMENT\",false#g" /var/www/html/conf/app.conf.php
# #RUN sed -i "s#DEBUG\",true#DEBUG\",false#g" /var/www/html/conf/app.conf.php


