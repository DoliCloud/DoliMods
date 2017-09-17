#!/bin/bash
#---------------------------------------------------------
# Script to update sources found into document dir
#---------------------------------------------------------

if [ "x$1" == "x" ]; then
   echo "Usage: $0  dir_documents"
   exit 1
fi

echo "Update git dirs found into $1"

#for dir in `find $1 -type d`
for dir in `ls -d $1/*`
do
	echo -- Process dir $dir
	cd $dir
	git pull --ff-only
	echo Result of git pull -ff-only = $?
	cd -
done

echo "Finished."
