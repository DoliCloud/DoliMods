#!/bin/bash

export target="dolibarr"
cd $target/src && tar --exclude-vcs -cvzf $target.tgz *
mv $target.tgz ../config
cd .. 
