@echo off
REM --------------------------------------------------------
REM This script start the Apache and Mysql DoliWamp services
REM --------------------------------------------------------

echo ---- Execute stopdoliwamp.bat >> doliwamp.log 2>>&1

echo NET STOP dolimedapache >> doliwamp.log 2>>&1
NET STOP dolimedapache
echo NET STOP dolimedmysqld >> doliwamp.log 2>>&1
NET STOP dolimedmysqld 

echo Please wait...
echo ---- End script >> doliwamp.log 2>>&1

REM sleep is not a Windows command
REM sleep 1
ping 1.1.1.1 -n 1 -w 1000 > nul