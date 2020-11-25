FROM php:7.4-apache

ARG DB_HOST="127.0.0.1"

LABEL Author="Sebas <sebastian.g.moran@gmail.com>"

RUN apt-get update &&\
    apt-get install --no-install-recommends --assume-yes --quiet \
    libpng-dev ca-certificates cron

WORKDIR /var/www/html/
RUN docker-php-ext-install mysqli gd

ADD src /var/www/html/
EXPOSE 80

# Configure cron
COPY src/doc/sifit.cron.d /etc/cron.d/sifit
RUN chmod 0644 /etc/cron.d/sifit
RUN touch /tmp/output-sifit.txt
RUN chown www-data /tmp/output-sifit.txt
RUN crontab /etc/cron.d/sifit

##
#CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
CMD cron && /usr/sbin/apache2ctl -D FOREGROUND
##

ENV MYSQL_ROOT_PASSWORD=root
ENV MYSQL_ROOT_USER=root

RUN sed -i 's#"HOME","/sifit"#"HOME",""#g' /var/www/html/conf/app.conf.php
RUN sed -i "s#\"DBServer\", \"localhost\"#\"DBServer\", \"$DB_HOST\"#g" /var/www/html/conf/app.conf.php
RUN sed -i "s#DEVELOPMENT\", true#DEVELOPMENT\", false#g" /var/www/html/conf/app.conf.php
RUN sed -i "s#DEBUG\", true#DEBUG\", false#g" /var/www/html/conf/app.conf.php


