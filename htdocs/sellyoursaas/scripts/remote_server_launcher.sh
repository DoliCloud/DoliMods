#!/usr/bin/env bash
#
# Script to launch SellyourSaas httpd daemon agent.
#

export now=`date +%Y%m%d%H%M%S`

echo
echo "**** ${0}"
#echo "${0} ${@}"
#echo "# User id --------> $(id -u)"
#echo "# Now ------------> $now"
#echo "# PID ------------> ${$}"
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
	echo "Usage: ${0##*/} start|stop|status"
fi

if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

if [ "x$1" == "xstart" ]; then
	#echo "socat TCP4-LISTEN:8080,fork EXEC:$scriptdir/remote_server.sh > /var/log/remote_server.log"
	#socat TCP4-LISTEN:8080,fork EXEC:$scriptdir/remote_server.sh & > /var/log/remote_server.log

	pid=`ps a | grep 'php -S 0.0.0.0' | grep -v grep | awk ' { print $1 } '`
	if [ "x$pid" == "x" ]; then
		echo Switch on directory $scriptdir
		cd $scriptdir
		php -S 0.0.0.0:8080 -t remote_server > /var/log/remote_server_php.log 2>&1 &
		echo "Server started with php -S 0.0.0.0:8080 -t remote_server > /var/log/remote_server_php.log 2&1"
	else
		echo Server is already running with PID $pid
	fi
fi

if [ "x$1" == "xstop" ]; then
	#killall socat
	
	pid=`ps a | grep 'php -S 0.0.0.0' | grep -v grep | awk ' { print $1 } '`
	if [ "x$pid" == "x" ]; then
		echo Server not started
	else
		echo Launch kill to stop server with PID $pid
		kill $pid
	fi
fi

if [ "x$1" == "xstatus" ]; then
	#killall socat
	
	pid=`ps a | grep 'php -S 0.0.0.0' | grep -v grep | awk ' { print $1 } '`
	if [ "x$pid" == "x" ]; then
		echo Server not started
	else
		echo Server run with PID $pid
	fi
	
fi
