#!/bin/bash
#---------------------------------------------------------
# Script to update sources found into document dir
#
# To include into cron
# /pathto/git_update_sources.sh documentdir/sellyoursaas/git > /pathto/git_update_sources.log 2>&
#---------------------------------------------------------

if [ "x$1" == "x" ]; then
   echo "Usage:   $0  dir_document_of_git_repositories"
   echo "Example: $0  /pathtodocuments/documents/sellyoursaas/git"
   exit 1
fi

echo "Update git dirs found into $1"

#for dir in `find $1 -type d`
for dir in `ls -d $1/*`
do
        echo -- Process dir $dir
        cd $dir
        git reset --hard HEAD
        git pull
        echo Result of git pull = $?

		echo "Clean some dirs to save disk spaces"
		rm -fr build/debian build/exe build/pad dev/ test/ doc/ htdocs/includes/ckeditor/ckeditor/adapters htdocs/includes/ckeditor/ckeditor/samples
		rm -fr htdocs/includes/sabre/sabre/dav/tests
		rm -fr htdocs/includes/tecnickcom/tcpdf/fonts/dejavu-fonts-ttf-* htdocs/includes/tecnickcom/tcpdf/fonts/freefont-* htdocs/includes/tecnickcom/tcpdf/fonts/ae_fonts_*
		
        if [ -s build/generate_filelist_xml.php ]; then
                echo "Found generate_filelist_xml.php"
                php build/generate_filelist_xml.php release=auto-dolicloud
        fi

        cd -
done

echo "Finished."
