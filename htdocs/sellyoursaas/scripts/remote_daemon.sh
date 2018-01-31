#!/usr/bin/env bash
#
# Script to launch bash httpd daemon.
#

export now=`date +%Y%m%d%H%M%S`

echo
echo "**** ${0}"
echo "${0} ${@}"
echo "# user id --------> $(id -u)"
echo "# now ------------> $now"
echo "# PID ------------> ${$}"
echo "# PWD ------------> $PWD" 
echo "# arguments ------> ${@}"
echo "# path to me -----> ${0}"
echo "# parent path ----> ${0%/*}"
echo "# my name --------> ${0##*/}"
echo "# realname -------> $(realpath ${0})"
echo "# realname name --> $(basename $(realpath ${0}))"
echo "# realname dir ---> $(dirname $(realpath ${0}))"

export PID=${$}
export scriptdir=$(dirname $(realpath ${0}))

if [ "x$1" == "xstart" ]; then
	echo "socat TCP4-LISTEN:8080,fork EXEC:$scriptdir/remote_server.sh"
	socat TCP4-LISTEN:8080,fork EXEC:$scriptdir/remote_server.sh &
fi

if [ "x$1" == "xstop" ]; then
	killall socat
fi

