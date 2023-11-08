# make wireguard-ip-calculator.py unreadable





#Update & upgrade linux
#Define which packet mamanger is insatlled
#Then install sudo, apache2, wireguard, curl, sudo, ls, rm, uptime, iptables, sar, grep, ip, free, awk, python3, mkdir, libapache2-mod-php
echo "Upgrading and installing required packages..."
if [ -x "$(command -v apk)" ];       then apk add --no-cache 
elif [ -x "$(command -v apt-get)" ]; then apt-get update; apt-get upgrade; apt-get install sudo apache2 wireguard curl iptables sysstat grep iproute2 original-awk python3 libapache2-mod-php git
elif [ -x "$(command -v dnf)" ];     then dnf install 
elif [ -x "$(command -v zypper)" ];  then zypper install 
else echo "FAILED TO INSTALL PACKAGES: Package manager not found. You must manually install: nano">&2; fi



#disable all except iptables



#Install requirements for python
echo "Satisfying python requirements..."
pip3 install netaddr
pip3 install ipaddress
pip3 install sys


# Allowing web server to manage /etc/wireguard folder
chown -R www-data: /etc/wireguard

echo "Downloading and installing QWire..."
# Colone QWire repository to user's foler and move to it
cd ~
git clone https://github.com/MagomedovTimur/QWire.git
cd QWire

rm -r /var/www/html/*

# Copy html folder to web server's folder
cp -r html/ /var/www/

cd ~
rm -r QWire

#Append QWire specific accesses to sudoers
echo "Adding rights to web server"
echo -e "\n#QWire specification" >> /etc/sudoers
echo -e "www-data ALL=(ALL) NOPASSWD: /usr/bin/wg-quick" >> /etc/sudoers
echo -e "www-data ALL=(ALL) NOPASSWD: /usr/bin/systemctl enable wg-quick" >> /etc/sudoers
echo -e "www-data ALL=(ALL) NOPASSWD: /usr/bin/systemctl disable wg-quick" >> /etc/sudoers
echo -e "www-data ALL=(ALL) NOPASSWD: /usr/bin/wg genkey" >> /etc/sudoers
echo -e "www-data ALL=(ALL) NOPASSWD: /usr/bin/wg pubkey" >> /etc/sudoers
echo -e "www-data ALL=(ALL) NOPASSWD: /usr/bin/wg show all\n" >> /etc/sudoers

#Append user's login/password to config.php
echo -n "Create QWire login: "
read -r login
echo -n "Create QWire password: "
read -r password
echo -e "<?php\n\$configUsername = \"$login\";\n\$configPassword = \"$password\";\n?>" > /var/www/html/config.php


echo -e "Restarting web server..."

