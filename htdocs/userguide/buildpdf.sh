#!/bin/bash
# Rem: To find invalid links, do "grep XRef" on output of this script

DIR=$(cd "$(dirname "$0")"; pwd)

echo Move to $DIR
cd "$DIR"

find . -type f -iname "*.asciidoc" -print0 | while IFS= read -r -d $'\0' file
do
	echo Process file $file
	python ./teclib-make-report --uselocalsheets --debug $file 2>&1
done

cd -
