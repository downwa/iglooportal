#!/bin/sh
# Install script

if [ "$1" = "--install" ]; then
	# git clone https://github.com/downwa/iglooportal # NOTE: Presumably already done if you are here

	# Install tcprelay from repository
	cd ..
	git clone https://github.com/downwa/tcprelay && cd tcprelay/ && ./configure && sudo make install && cd ..
	cd iglooportal/

	pwd

	# Install or update needed software
	sudo apt-get --assume-yes install arptables bridge-utils conntrack dnsutils ebtables git nmap realpath dnsmasq isc-dhcp-server lighttpd php5-cgi inotify-tools

	# Fix permissions before copy
	sudo chown root etc/sudoers.d/
	sudo chown root ./etc/sudoers.d/www-data-allow

	# Enable PHP for lighttpd
	ln -s /etc/lighttpd/conf-available/10-fastcgi.conf /etc/lighttpd/conf-enabled/
	ln -s /etc/lighttpd/conf-available/15-fastcgi-php.conf /etc/lighttpd/conf-enabled/
fi

sudo cp -av etc/ usr/ home/ var/ /
sudo /etc/init.d/iglooportal restart
sudo ifup -a
sudo /etc/init.d/isc-dhcp-server start
