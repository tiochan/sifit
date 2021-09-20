#!/bin/sh
#
# https://dev.mysql.com/doc/refman/8.0/en/docker-mysql-getting-started.html
#

APP_DIR="../../src"
PASS="password"

docker pull mysql/mysql-server:5.7
ps=$(docker run --name=sifit_mysql -p 3306:3306 -d mysql/mysql-server:5.7)
echo $ps
echo "Waiting ..."
sleep 20
pass=$(docker logs $ps 2>&1 | grep GENERATED | awk '{print $5}')
docker exec -i $ps mysql -uroot -p"${pass}" --connect-expired-password < change_password.sql

echo $pass

# echo "drop database sifit" | mysql -psifit05
docker exec -i $ps mysql -uroot -p${PASS} < ${APP_DIR}/doc/sifit_create_user.sql
docker exec -i $ps mysql -uroot -p${PASS} sifit < ${APP_DIR}/doc/sifit.sql

docker exec -it $ps mysql -uroot -p${PASS}


# docker run --name=mysql80 \
#   --mount type=bind,src=/path-on-host-machine/my.cnf,dst=/etc/my.cnf \
#   --mount type=bind,src=/path-on-host-machine/datadir,dst=/var/lib/mysql \        
#   -d mysql/mysql-server:8.0
