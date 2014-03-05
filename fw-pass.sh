#!/bin/sh

IPTABLES=/sbin/iptables
INET=eth0
CLI=eth1
RIP=192.168.42.1

iptables -P INPUT ACCEPT
iptables -P FORWARD ACCEPT
iptables -P OUTPUT ACCEPT
# Flush and delete chains
iptables -t filter -F 
iptables -t filter -X
iptables -t nat    -F
iptables -t nat    -X
iptables -t mangle -F
iptables -t mangle -X
iptables -t raw    -F
iptables -t raw    -X

#iptables-restore </etc/iptables.ipv4.nat

# Create table of authorized clients, populate it from flat file, and check them
$IPTABLES -t nat    -N auth
#  $IPTABLES -t nat    -A PREROUTING  -i $CLI  -o $INET -j auth  #REJECT #ACCEPT # FORWARD authorized clients only
$IPTABLES -t nat    -A PREROUTING  -i $CLI -j auth  #REJECT #ACCEPT # FORWARD authorized clients only

# Non-authorized clients get DNS only then are redirected to login page
$IPTABLES -t nat    -A PREROUTING -p udp --dport    53 -j ACCEPT
$IPTABLES -t nat    -A PREROUTING -p tcp --dport    80 -j DNAT --to-destination $RIP
$IPTABLES -t nat    -A PREROUTING -p tcp --dport   443 -j DNAT --to-destination $RIP
$IPTABLES -t nat    -A PREROUTING -p tcp --dport 24800 -j ACCEPT    # FOR DEBUGGING ONLY
$IPTABLES -t nat    -A PREROUTING -j DNAT --to-destination $RIP

# Enable Internet connection sharing
echo "1" > /proc/sys/net/ipv4/ip_forward
$IPTABLES -t filter -A FORWARD     -i $INET -o $CLI -m state --state ESTABLISHED,RELATED -j ACCEPT
$IPTABLES -t nat    -A POSTROUTING -o $INET -j MASQUERADE

#For even authorized clients, limit services that can be accessed
$IPTABLES -t nat    -N serv 
$IPTABLES -t nat    -A serv -p udp --dport    53 -j ACCEPT
$IPTABLES -t nat    -A serv -p tcp --dport    80 -j ACCEPT
$IPTABLES -t nat    -A serv -p tcp --dport   443 -j ACCEPT
$IPTABLES -t nat    -A serv -p tcp --dport 24800 -j ACCEPT      # FOR DEBUGGING ONLY
$IPTABLES -t nat    -A serv -p icmp -j ACCEPT
$IPTABLES -t nat    -A serv -j DNAT --to-destination $RIP


# Allow specific clients
mac=f0:4d:a2:7f:66:77
$IPTABLES -t nat -A auth -m mac --mac-source $mac -j serv

	#$IPTABLES -I FORWARD 1 -t filter -m mac --mac-source $mac -j ACCEPT # RETURN
	#$IPTABLES -I FORWARD 1 -t filter -m mark --mark 99 -p tcp --dport 80

	#$IPTABLES -I FORWARD 1 -t filter -m mac --mac-source $mac -p tcp --dport 80 -j ACCEPT
	#$IPTABLES -I FORWARD 1 -t filter -m mac --mac-source $mac -p tcp --dport 443 -j ACCEPT
	#$IPTABLES -I FORWARD 1 -t filter -m mac --mac-source $mac -p udp --dport 53  -j ACCEPT
	#$IPTABLES -I FORWARD 1 -t filter -m mac --mac-source $mac -p tcp --dport 24800 -j ACCEPT
	#$IPTABLES -I FORWARD 1 -t filter -m mac --mac-source $mac -p icmp -j ACCEPT

