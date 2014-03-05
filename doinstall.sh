#!/bin/sh

if [ "$1" = "--install" ]; then
	# Install needed software
	apt-get install arptables bridge-utils conntrack dnsutils ebtables git nmap realpath dnsmasq isc-dhcp-server
fi

cp -av etc/ usr/ /
/etc/init.d/iglooportal restart

