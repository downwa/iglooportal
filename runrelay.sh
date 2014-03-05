#!/bin/sh

main() {
        /usr/local/bin/tcprelay --listen-port 80 --server 192.168.200.11:8080 --ip-as-port --minimal-log --quiet 2>/var/log/tcprelay80.log &
        /usr/local/bin/tcprelay --listen-port 443 --server 192.168.200.11:447 --ip-as-port --minimal-log --quiet 2>/var/log/tcprelay443.log &
        /usr/local/bin/tcprelay --listen-port 447 --server 67.58.20.7:447 --ip-as-port --minimal-log --quiet 2>/var/log/tcprelay447.log &
        sleep 1
        netstat -nlp | egrep ":80|:443|:447"
}

main
