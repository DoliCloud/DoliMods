@echo off
REM --------------------------------------------------------
REM This script start the Apache and Mysql DoliWamp services
REM --------------------------------------------------------

echo ---- Execute startdoliwamp.bat >> doliwamp.log 2>>&1

echo NET START dolimedapache >> doliwamp.log 2>>&1
NET START dolimedapache >> doliwamp.log 2>>&1
echo NET START dolimedmysqld >> doliwamp.log 2>>&1
NET START dolimedmysqld >> doliwamp.log 2>>&1

echo Please wait...
echo ---- End script >> doliwamp.log 2>>&1

REM sleep is not a Windows commande
REM sleep 1
ping 1.1.1.1 -n 1 -w 1000 > nul