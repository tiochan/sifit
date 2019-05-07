#!/bin/sh

[ ! $(whoami) = "root" ] && echo "Must be run as root (sudo)" && exit 1

apt-get remove --purge -y apache2 php* mysql* libapache2-mod-php*
sudo apt-get autoremove --purge
