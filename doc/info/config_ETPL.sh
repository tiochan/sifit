#!/bin/sh

USER="eminder"
POWEROFF="poweroff"
HIBERNATE="pm-hibernate"

if [ `whoami` != 'root' ]; then
   echo "Cal executar-ho com a root"
   echo "Torneu-ho a executar amb sudo:"
   echo "sudo $0"
fi

echo "Instal·lant, si no existeix, el software de gestió d'energia"
apt-get -qy install pm-utils acpi-support > /dev/null

POWEROFF_CMD=`which ${POWEROFF}`
HIBERNATE_CMD=`which ${HIBERNATE}`

if [ "$POWEROFF_CMD" = "" ]; then
   echo "ATENCIO: No puc trobar la comanda ${POWEROFF}"
   echo "Això implica que no funcionarà la funcionalitat d'auto apagat remot"
fi

if [ "$HIBERNATE_CMD" = "" ]; then
   echo "ATENCIO: No puc trobar la comanda ${HIBERNATE}"
   echo "Això implica que no funcionarà la funcionalitat d'hibernació remota"
fi

if [ ! `egrep "${USER}" /etc/passwd` ]; then
   echo "Creant l'usuari ${USER}"
   groupadd ${USER}
   useradd ${USER} -g ${USER} --create-home --shell /bin/bash
else
   echo "L'usuari eminder ja existeix"
fi

echo "Afegint els permisos d'estalvi energètic"
egrep "^#includedir /etc/sudoers.d" /etc/sudoers > /dev/null

if [ $? -eq 1 ]; then
	echo "" >> /etc/sudoers
	echo "#includedir /etc/sudoers.d" >> /etc/sudoers
fi

touch /etc/sudoers.d/${USER}
chmod 0440 /etc/sudoers.d/${USER}
echo "${USER} ALL = (ALL) NOPASSWD: /sbin/poweroff, /usr/sbin/pm-hibernate, /bin/ls" > /etc/sudoers.d/${USER}

echo "Afegint les claus d'accés remot al servei e-minder"
mkdir -p /home/${USER}/.ssh
touch /home/${USER}/.ssh/authorized_keys
chown -R ${USER}:${USER} /home/${USER}/.ssh
echo "ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEA1VPPOozopw3CZ3bklMuellrQMW6gGofYCkFAjgvZL94xQcuEqttB07jaDxlQNW0D1mwVKJqbj8oLw9+7kxhlMQLzlc/O6tcy+9FVhrBOD9XivUU8x7bIyEoVcJXHgwJv9PTKqxBI21HZqNSWj3yV+RyKN9hLNaj6Oxnwho8b/jcLlAdL/y3R9/xDN9l5l71a8+Z0LS3cBNKGY0NrHpQtikCuhCw542K2KLz3QZdolVJDXIWEOrPSZvBYRrfyHJbgewTUpjfF/YouJwVK4YcsAkxf4prSH0Phlc+aAd+ilfNLNP4FtTAoJZH7q1+io25pUTTdlaGVJhyz/L0Uic/h+w== root@sigvi" >> /home/${USER}/.ssh/authorized_keys

echo "Canviant la configuració del gdm"
sudo gconftool-2 --direct --config-source xml:readwrite:/etc/gconf/gconf.xml.mandatory --type Boolean --set /apps/gdm/simple-greeter/disable_user_list True 


echo "Fet"
echo ""
echo "Ara, si no ho has fet ja, t'hauràs de donar d'alta al servei:"
echo "https://e-minder.upcnet.es/admin/wol.php"
