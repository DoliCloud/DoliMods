#!/bin/bash
cd dolibarr/src
tar --exclude-vcs -acvf dolibarr.tgz *
mv dolibarr.tgz ../config
cd .. 
