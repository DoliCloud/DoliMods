#!/usr/bin/env bash
#
# Script to launch bash httpd daemon.
#

export now=`date +%Y%m%d%H%M%S`

echo
echo "**** ${0}"
#echo "${0} ${@}"
echo "# User id --------> $(id -u)"
#echo "# Now ------------> $now"
echo "# PID ------------> ${$}"
#echo "# PWD ------------> $PWD" 
#echo "# arguments ------> ${@}"
#echo "# path to me -----> ${0}"
#echo "# parent path ----> ${0%/*}"
#echo "# my name --------> ${0##*/}"
#echo "# realname -------> $(realpath ${0})"
#echo "# realname name --> $(basename $(realpath ${0}))"
#echo "# realname dir ---> $(dirname $(realpath ${0}))"

export PID=${$}
export scriptdir=$(dirname $(realpath ${0}))

if [ "x$1" == "x" ]; then
	echo "Usage: ${0##*/} start|stop"
fi

if [ "x$1" == "xstart" ]; then
	echo "socat TCP4-LISTEN:8080,fork EXEC:$scriptdir/remote_server.sh"
	socat TCP4-LISTEN:8080,fork EXEC:$scriptdir/remote_server.sh & > /var/log/remote_server.log
fi

if [ "x$1" == "xstop" ]; then
	killall socat
fi

