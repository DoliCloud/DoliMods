#!/bin/bash
#---------------------------------------------------------
# Script to update sources found into document dir
#
# To include into cron
# /pathto/git_update_sources.sh documentdir/sellyoursaas/git > /pathto/git_update_sources.log 2>&
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
	git pull --ff-only --depth=10
	echo Result of git pull -ff-only = $?
	cd -
done

echo "Finished."
