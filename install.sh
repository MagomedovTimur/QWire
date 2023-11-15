# Define which linux distribution is insatlled
# Update & upgrade linux
# Then install sudo, apache2, wireguard, curl, sudo, ls, rm, uptime, iptables, sar, grep, ip, free, awk, python3, mkdir, libapache2-mod-php
QWire_OS=`cat /etc/os-release | awk '/^ID=/{print substr($0,4)}'`

case $QWire_OS  in
    *"debian"*)
		apt-get -y install sudo apache2 wireguard curl iptables sysstat grep iproute2 python3 libapache2-mod-php git iptables-persistent
        ;;
    *)
        echo "Failed to install packages: Linux distribution is not supported by this script"
        echo "Try to install QWire manually"
        ;;
esac


echo "Setting up the iptables configuration..."
# Wipe iptables
iptables -P INPUT ACCEPT
iptables -P OUTPUT ACCEPT
iptables -P FORWARD ACCEPT
iptables -F

# Drop invalid packets
iptables -A INPUT -m conntrack --ctstate INVALID -j DROP

# Allow legit established connections
iptables -A INPUT -m conntrack --ctstate ESTABLISHED,RELATED -j ACCEPT

# Allow ssh
iptables -A INPUT -p tcp -m tcp --dport 22 -j ACCEPT

# Allow http
iptables -A INPUT -p tcp --dport 80 -m conntrack --ctstate NEW,ESTABLISHED -j ACCEPT

# Drop all packets by default
iptables -P INPUT DROP
iptables -P FORWARD DROP

# Save iptables config
iptables-save > /etc/iptables/rules.v4

# Install requirements for python
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

# Remove QWire download folder and installing script
cd ~
rm -r QWire
rm QWire_temp.sh

# Append QWire specific accesses to sudoers
echo "Adding rights to web server"
echo -e "\n#QWire specification" >> /etc/sudoers
echo -e "www-data ALL=(ALL) NOPASSWD: /usr/bin/wg-quick" >> /etc/sudoers
echo -e "www-data ALL=(ALL) NOPASSWD: /usr/bin/systemctl enable wg-quick" >> /etc/sudoers
echo -e "www-data ALL=(ALL) NOPASSWD: /usr/bin/systemctl disable wg-quick" >> /etc/sudoers
echo -e "www-data ALL=(ALL) NOPASSWD: /usr/bin/wg genkey" >> /etc/sudoers
echo -e "www-data ALL=(ALL) NOPASSWD: /usr/bin/wg pubkey" >> /etc/sudoers
echo -e "www-data ALL=(ALL) NOPASSWD: /usr/bin/wg show all\n" >> /etc/sudoers

# Append user's login/password to config.php
echo -n "Create QWire login: "
read -r login
echo -n "Create QWire password: "
read -r password
echo -e "<?php\n\$configUsername = \"$login\";\n\$configPassword = \"$password\";\n?>" > /var/www/html/config.php


echo -e "Restarting web server..."
systemctl restart apache2
