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
	sudo apt-get --assume-yes install arptables bridge-utils conntrack dnsutils ebtables git nmap realpath dnsmasq isc-dhcp-server

	# Fix permissions before copy
	sudo chown root etc/sudoers.d/
	sudo chown root ./etc/sudoers.d/www-data-allow
fi

sudo cp -av etc/ usr/ home/ /
sudo /etc/init.d/iglooportal restart
sudo ifup -a
sudo /etc/init.d/isc-dhcp-server start

