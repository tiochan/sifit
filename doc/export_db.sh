#!/bin/bash
echo -n "mysql pass: "
read -s MY_PWD

sudo mysqldump -p${MY_PWD} sifit > /tmp/sifit.sql
echo "drop database if exists sifit_tmp; create database sifit_tmp" | sudo mysql -p${MY_PWD}
sudo mysql -p${MY_PWD} sifit_tmp < /tmp/sifit.sql
echo "delete from report_tags where is_protected=0;" | sudo mysql -p${MY_PWD} sifit_tmp
sudo mysqldump -p${MY_PWD} sifit_tmp > sifit.sql
sudo mysqldump -p${MY_PWD} sifit report_tags > sifit_report_tags.sql
#echo "drop database if exists sifit_tmp; create database sifit_tmp" | sudo mysql -p${MY_PWD}
rm /tmp/sifit.sql
