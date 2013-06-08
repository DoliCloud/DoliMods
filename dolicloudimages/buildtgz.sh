#!/bin/bash
cd dolibarr/src && tar --exclude-vcs -cvzf dolibarr.tgz *
mv dolibarr.tgz ../config
cd .. 
