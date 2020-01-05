#!/bin/sh

[ ! $(whoami) = "root" ] && echo "Must be run as root (sudo)" && exit 1

APP_NAME="sifit"
GIT_BRANCH="	"
APP_DIR=/var/www/html/${APP_NAME}

## Git
if [ ! -d ${APP_DIR} ]; then
	mkdir -p ${APP_DIR}
	cd $(dirname ${APP_DIR})
	git clone -b ${GIT_BRANCH} https://github.com/tiochan/sifit
else
	git fetch --all -b ${GIT_BRANCH} https://github.com/tiochan/sifit
	git reset --hard origin/${GIT_BRANCH}
fi

## Web server
apt-get -y install libapache2-mod-php php-mysql php-cli php-ldap php-gd php-json php-cli git

## Configure
#sed -i 's#"/sifit"#""#g' /var/www/conf/app.conf.php

## Crontab
echo "0,30 * * * *  www-data /usr/bin/php -f /var/www/html/sifit/cron/launch_processes.php > /tmp/output-sifit.txt 2>&1" > /etc/cron.d/sifit

## MYSQL
if [ ! "$(dpkg -l | grep mysql-server)" ]; then
	echo 'mysql-server mysql-server/root_password password sifit05' | debconf-set-selections
	echo 'mysql-server mysql-server/root_password_again password sifit05' | debconf-set-selections
	apt-get -y install mysql-server
	
	sed -i 's#127.0.0.1#*#g' /etc/mysql/my.cnf
	service mysql restart
fi
## Config

## SIFIT DB
echo "drop database sifit" | mysql -psifit05
mysql -psifit05 < ${APP_DIR}/doc/sifit_create_user.sql
mysql -psifit05 sifit < ${APP_DIR}/doc/sifit.sql
