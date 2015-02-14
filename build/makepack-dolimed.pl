#!/usr/bin/perl
#----------------------------------------------------------------------------
# \file         build/makepack-dolimed.pl
# \brief        DoliMed package builder (tgz, zip, exe)
# \author       (c)2005-2014 Laurent Destailleur  <eldy@users.sourceforge.net>
#
# This is list of constant you can set to have generated packages moved into a specific dir: 
#DESTIBETARC='/media/HDDATA1_LD/Mes Sites/Web/Dolibarr/dolibarr.org/files/lastbuild'
#DESTISTABLE='/media/HDDATA1_LD/Mes Sites/Web/Dolibarr/dolibarr.org/files/stable'
#DESTIMODULES='/media/HDDATA1_LD/Mes Sites/Web/Admin1/wwwroot/files/modules'
#DESTIDOLIMEDBETARC='/media/HDDATA1_LD/Mes Sites/Web/DoliCloud/dolimed.com/htdocs/files/lastbuild'
#DESTIDOLIMEDMODULES='/media/HDDATA1_LD/Mes Sites/Web/DoliCloud/dolimed.com/htdocs/files/modules'
#DESTIDOLIMEDSTABLE='/media/HDDATA1_LD/Mes Sites/Web/DoliCloud/dolimed.com/htdocs/files/stable'
#----------------------------------------------------------------------------

use Cwd;
$OWNER="ldestailleur";
$GROUP="ldestailleur";


@LISTETARGET=("TGZ","ZIP","EXEDOLIWAMP");   # Possible packages
%REQUIREMENTTARGET=(                        # Tool requirement for each package
"SNAPSHOT"=>"tar",
"TGZ"=>"tar",
"ZIP"=>"7z",
"EXEDOLIWAMP"=>"ISCC.exe"
);
%ALTERNATEPATH=(
"7z"=>"7-ZIP",
"makensis.exe"=>"NSIS"
);


use vars qw/ $REVISION $VERSION /;
$REVISION='1.0';
$VERSION="3.5 (build $REVISION)";



#------------------------------------------------------------------------------
# MAIN
#------------------------------------------------------------------------------
($DIR=$0) =~ s/([^\/\\]+)$//; ($PROG=$1) =~ s/\.([^\.]*)$//; $Extension=$1;
$DIR||='.'; $DIR =~ s/([^\/\\])[\\\/]+$/$1/;

# Detect OS type
# --------------
if ("$^O" =~ /linux/i || (-d "/etc" && -d "/var" && "$^O" !~ /cygwin/i)) { $OS='linux'; $CR=''; }
elsif (-d "/etc" && -d "/Users") { $OS='macosx'; $CR=''; }
elsif ("$^O" =~ /cygwin/i || "$^O" =~ /win32/i) { $OS='windows'; $CR="\r"; }
if (! $OS) {
    print "$PROG.$Extension was not able to detect your OS.\n";
	print "Can't continue.\n";
	print "$PROG.$Extension aborted.\n";
    sleep 2;
	exit 1;
}

# Define buildroot
# ----------------
if ($OS =~ /linux/) {
    $TEMP=$ENV{"TEMP"}||$ENV{"TMP"}||"/tmp";
}
if ($OS =~ /macos/) {
    $TEMP=$ENV{"TEMP"}||$ENV{"TMP"}||"/tmp";
}
if ($OS =~ /windows/) {
    $TEMP=$ENV{"TEMP"}||$ENV{"TMP"}||"c:/temp";
    $PROGPATH=$ENV{"ProgramFiles"};
}
if (! $TEMP || ! -d $TEMP) {
    print "Error: A temporary directory can not be find.\n";
    print "Check that TEMP or TMP environment variable is set correctly.\n";
	print "$PROG.$Extension aborted.\n";
    sleep 2;
    exit 2;
} 
$BUILDROOT="$TEMP/buildroot";


my $copyalreadydone=0;      # Use "-" before number of choice to avoid copy
my $batch=0;
for (0..@ARGV-1) {
	if ($ARGV[$_] =~ /^-*target=(\w+)/i)   { $target=$1; $batch=1; }
	if ($ARGV[$_] =~ /^-*desti=(.+)/i)     { $DESTI=$1; }
    if ($ARGV[$_] =~ /^-*prefix=(.+)/i)    {
    	$PREFIX=$1; 
    	$FILENAMESNAPSHOT.="-".$PREFIX; 
    }
}

$SOURCEMOD="$DIR/..";
$SOURCEMOD1="$DIR/../htdocs/cabinetmed";
$SOURCEMOD2="$DIR/../build/exe/dolimed";
# Change SOURCEDOL to use another dolibarr source directory
$SOURCEDOL="$DIR/../../dolibarr_3.7/.";	

if (! -d $ENV{"DESTIDOLIMEDBETARC"} || ! -d $ENV{"DESTIDOLIMEDSTABLE"})
{
    print "Error: Directory of environment variable DESTIBETARC or DESTISTABLE does not exist.\n";
	print "$PROG.$Extension aborted.\n";
    sleep 2;
	exit 1;
}


print "Makepack version $VERSION\n";
print "Source directory dolibarr   (SOURCEDOL) : $SOURCEDOL\n";
print "Source directory cabinetmed (SOURCEMOD1): $SOURCEMOD1\n";
print "Source directory cabinetmed (SOURCEMOD2): $SOURCEMOD2\n";


$PROJECT="dolimed";
$PROJECTBIS='CabinetMed';

# Loop on each projects
$PROJECTLC=lc($PROJECTBIS);
	
# Get version $MAJOR, $MINOR and $BUILD
$result=open(IN,"<".$SOURCEMOD1."/core/modules/mod".$PROJECTBIS.".class.php");
if (! $result) { die "Error: Can't open descriptor file ".$SOURCE."/htdocs/".$PROJECTLC."/core/modules/mod".$PROJECTBIS.".class.php for reading.\n"; }
while(<IN>)
{
  	if ($_ =~ /this->version\s*=\s*'([\d\.]+)'/) { $PROJVERSION=$1; break; }
}
close IN;

($MAJOR,$MINOR,$BUILD)=split(/\./,$PROJVERSION,3);
if ($MINOR eq '')
{
    print "Enter value for minor version for module ".$PROJECT.": ";
    $MINOR=<STDIN>;
    chomp($MINOR);
}
	

$DESTI="$DIR/../build";
if ($ENV{"DESTIDOLIMEDBETARC"} && $BUILD =~ /[a-z]/i)    { $DESTI = $ENV{"DESTIDOLIMEDBETARC"}; }	# Force output dir if env DESTI is defined
if ($ENV{"DESTIDOLIMEDSTABLE"} && $BUILD =~ /^[0-9]+$/)  { $DESTI = $ENV{"DESTIDOLIMEDSTABLE"}; }	# Force output dir if env DESTI is defined
$NEWDESTI=$DESTI;
print "Target directory: $NEWDESTI\n";
	
$FILENAME="$PROJECT";
$FILENAMESNAPSHOT="$PROJECT-snapshot";
$FILENAMETGZ="$PROJECT-$MAJOR.$MINOR.$BUILD";
$FILENAMEZIP="$PROJECT-$MAJOR.$MINOR.$BUILD";
$FILENAMEXZ="$PROJECT-$MAJOR.$MINOR.$BUILD";
$FILENAMERPM="$PROJECT-$MAJOR.$MINOR.$BUILD-$RPMSUBVERSION";
$FILENAMEDEB="${PROJECT}_${MAJOR}.${MINOR}.${BUILD}";
$FILENAMEAPS="$PROJECT-$MAJOR.$MINOR.$BUILD.app";
$FILENAMEEXEDOLIWAMP="DoliMed-$MAJOR.$MINOR.$BUILD";
if (-d "/usr/src/redhat")   { $RPMDIR="/usr/src/redhat"; } # redhat
if (-d "/usr/src/packages") { $RPMDIR="/usr/src/packages"; } # opensuse
if (-d "/usr/src/RPM")      { $RPMDIR="/usr/src/RPM"; } # mandrake


print "Building package name: $PROJECT\n";
print "Building package version: $MAJOR.$MINOR.$BUILD\n";



# Choose package targets
#-----------------------
if ($target) {
    $CHOOSEDTARGET{uc($target)}=1;
}
else {
    my $found=0;
    my $NUM_SCRIPT;
    while (! $found) {
    	my $cpt=0;
    	printf(" %2d - %-12s    (%s)\n",$cpt,"All (Except SNAPSHOT)","Need ".join(",",values %REQUIREMENTTARGET));
    	foreach my $target (@LISTETARGET) {
    		$cpt++;
    		printf(" %2d - %-12s    (%s)\n",$cpt,$target,"Need ".$REQUIREMENTTARGET{$target});
    	}
    
    	# On demande de choisir le fichier à passer
    	print "Choose one package number or several separated with space (0 - ".$cpt."): ";
    	$NUM_SCRIPT=<STDIN>; 
    	chomp($NUM_SCRIPT);
    	if ($NUM_SCRIPT =~ s/-//g) {
    		# Do not do copy	
    		$copyalreadydone=1;
    	}
    	if ($NUM_SCRIPT !~ /^[0-9\s]+$/)
    	{
    		print "This is not a valid package number list.\n";
    		$found = 0;
    	}
    	else
    	{
    		$found = 1;
    	}
    }
    print "\n";
    if ($NUM_SCRIPT) {
    	foreach my $num (split(/\s+/,$NUM_SCRIPT)) {
    		$CHOOSEDTARGET{$LISTETARGET[$num-1]}=1;
    	}
    }
    else {
    	foreach my $key (@LISTETARGET) {
    		if ($key ne 'SNAPSHOT') { $CHOOSEDTARGET{$key}=1; }
        }
    }
}


# Test if requirement is ok
#--------------------------
$atleastonerpm=0;
foreach my $target (keys %CHOOSEDTARGET) {
	if ($target =~ /RPM/i)
	{
		if ($atleastonerpm && ($DESTIMOD eq ""))
		{
			print "Error: You asked creation of several rpms. Because all rpm have same name, you must defined an environment variable DESTI to tell packager where it can create subdirs for each generated package.\n";
			exit;
		}
		$atleastonerpm=1;			
	} 
    foreach my $req (split(/[,\s]/,$REQUIREMENTTARGET{$target})) {
        # Test    
        print "Test requirement for target $target: Search '$req'... ";
        $newreq=$req; $newparam='';
        if ($newreq eq 'zip') { $newparam.='-h'; }
        if ($newreq eq 'xz') { $newparam.='-h'; }
        $cmd="\"$newreq\" $newparam 2>&1";
        print "Test command ".$cmd."... ";
        $ret=`$cmd`;
        $coderetour=$?; $coderetour2=$coderetour>>8;
        if ($coderetour != 0 && (($coderetour2 == 1 && $OS =~ /windows/ && $ret !~ /Usage/i) || ($coderetour2 == 127 && $OS !~ /windows/)) && $PROGPATH) { 
            # Not found error, we try in PROGPATH
            $ret=`"$PROGPATH/$ALTERNATEPATH{$req}/$req\" 2>&1`;
            $coderetour=$?; $coderetour2=$coderetour>>8;
            $REQUIREMENTTARGET{$target}="$PROGPATH/$ALTERNATEPATH{$req}/$req";
        } 

        if ($coderetour != 0 && (($coderetour2 == 1 && $OS =~ /windows/ && $ret !~ /Usage/i) || ($coderetour2 == 127 && $OS !~ /windows/))) {
            # Not found error
            print "Not found\nCan't build target $target. Requirement '$req' not found in PATH\n";
            $CHOOSEDTARGET{$target}=-1;
            last;
        } else {
            # Pas erreur ou erreur autre que programme absent
            print " Found ".$REQUIREMENTTARGET{$target}."\n";
        }
    }
}

print "\n";

# Check if there is at least on target to build
#----------------------------------------------
$nboftargetok=0;
$nboftargetneedbuildroot=0;
$nboftargetneedcvs=0;
foreach my $target (keys %CHOOSEDTARGET) {
    if ($CHOOSEDTARGET{$target} < 0) { next; }
	#if ($target ne 'EXE' && $target ne 'EXEDOLIWAMP') 
	#{
		$nboftargetneedbuildroot++;
	#}
	#if ($target eq 'SNAPSHOT')
	#{
	#	$nboftargetneedcvs++;
	#}
	$nboftargetok++;
}

if ($nboftargetok) {

    # Update CVS if required
    #-----------------------
    if ($nboftargetneedcvs)
	{
    	print "Go to directory $SOURCE\n";
   		$olddir=getcwd();
   		chdir("$SOURCE");
    	print "Run cvs update -P -d\n";
    	$ret=`cvs update -P -d 2>&1`;
    	chdir("$olddir");
	}
	
    # Update buildroot if required
    #-----------------------------
    if ($nboftargetneedbuildroot)
	{
	    if (! $copyalreadydone) {
	    	print "Creation of a buildroot used for all packages\n";

	    	print "Delete directory $BUILDROOT\n";
	    	$ret=`rm -fr "$BUILDROOT"`;
	    
	    	mkdir "$BUILDROOT";
	    	mkdir "$BUILDROOT/$PROJECT";
	    	mkdir "$BUILDROOT/$PROJECT/htdocs";
	    	mkdir "$BUILDROOT/$PROJECT/build";
	    	mkdir "$BUILDROOT/$PROJECT/build/exe";
	    	print "Copy $SOURCEDOL into $BUILDROOT/$PROJECT\n";
	    	$ret=`cp -pr "$SOURCEDOL" "$BUILDROOT/$PROJECT"`;
	    	print "Copy $SOURCEMOD1 into $BUILDROOT/$PROJECT/htdocs\n";
	    	$ret=`cp -pr "$SOURCEMOD1" "$BUILDROOT/$PROJECT/htdocs"`;
	    	print "Copy $SOURCEMOD2 into $BUILDROOT/$PROJECT/build/exe\n";
	    	$ret=`cp -pr "$SOURCEMOD2" "$BUILDROOT/$PROJECT/build/exe"`;

			# Specific to DoliMed
	    	print "Copy $SOURCEMOD1/img/dolimed_logo.png into $BUILDROOT/$PROJECT/htdocs/theme/dolibarr_logo.png\n";
	    	$ret=`cp -pf "$SOURCEMOD1/img/dolimed_logo.png" "$BUILDROOT/$PROJECT/htdocs/theme/dolibarr_logo.png"`;
	    	print "Copy $SOURCEMOD1/img/dolimed.png into $BUILDROOT/$PROJECT/htdocs/theme/dolibarr.png\n";
	    	$ret=`cp -pf "$SOURCEMOD1/img/dolimed.png" "$BUILDROOT/$PROJECT/htdocs/theme/dolibarr.png"`;
	    	print "Copy $SOURCEMOD1/img/dolimed.png into $BUILDROOT/$PROJECT/htdocs/theme/dolibarr.png\n";
	    	$ret=`cp -pf "$SOURCEMOD1/img/dolimed.png" "$BUILDROOT/$PROJECT/htdocs/theme/dolibarr.png"`;
			
	    }
	    print "Clean $BUILDROOT\n";
	    $ret=`rm -f  $BUILDROOT/$PROJECT/.buildpath`;
	    $ret=`rm -fr $BUILDROOT/$PROJECT/.cache`;
	    $ret=`rm -fr $BUILDROOT/$PROJECT/.git`;
	    $ret=`rm -f  $BUILDROOT/$PROJECT/.gitmodules`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/.gitignore`;
	    $ret=`rm -fr $BUILDROOT/$PROJECT/.project`;
	    $ret=`rm -fr $BUILDROOT/$PROJECT/.settings`;
	    $ret=`rm -fr $BUILDROOT/$PROJECT/.tx`;
	    $ret=`rm -f  $BUILDROOT/$PROJECT/build.xml`;
	    $ret=`rm -f  $BUILDROOT/$PROJECT/quickbuild.xml`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/pom.xml`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/README.md`;
        
	    $ret=`rm -fr $BUILDROOT/$PROJECT/build/html`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/build/Doli*-*`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/build/dolibarr_*.deb`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/build/dolibarr_*.dsc`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/build/dolibarr_*.tar.gz`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/build/dolibarr-*.deb`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/build/dolibarr-*.rpm`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/build/dolibarr-*.tar`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/build/dolibarr-*.tar.gz`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/build/dolibarr-*.tgz`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/build/dolibarr-*.xz`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/build/dolibarr-*.zip`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/build/doxygen/doxygen_warnings.log`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/htdocs/cache.manifest`;
	    $ret=`rm -f  $BUILDROOT/$PROJECT/htdocs/conf/conf.php`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/htdocs/conf/conf.php.mysql`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/htdocs/conf/conf.php.old`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/htdocs/conf/conf.php.postgres`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/htdocs/conf/conf*sav*`;

        $ret=`rm -f  $BUILDROOT/$PROJECT/htdocs/install/mssql/README`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/htdocs/install/mysql/README`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/htdocs/install/pgsql/README`;

        $ret=`rm -fr $BUILDROOT/$PROJECT/dev/codesniffer`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/dev/codetemplates`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/dev/dbmodel`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/dev/initdata`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/dev/iso-normes`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/dev/ldap`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/dev/licence`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/dev/mail`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/dev/phpcheckstyle`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/dev/phpunit`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/dev/security`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/dev/spec`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/dev/test`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/dev/uml`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/dev/xdebug`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/dev/dolibarr_changes.txt`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/dev/README`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/doc/images/dolibarr_screenshot2.png`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/doc/images/dolibarr_screenshot3.png`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/doc/images/dolibarr_screenshot4.png`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/doc/images/dolibarr_screenshot5.png`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/doc/images/dolibarr_screenshot6.png`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/doc/images/dolibarr_screenshot7.png`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/doc/images/dolibarr_screenshot8.png`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/doc/images/dolibarr_screenshot9.png`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/doc/images/dolibarr_screenshot10.png`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/doc/images/dolibarr_screenshot11.png`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/doc/images/dolibarr_screenshot12.png`;

	    $ret=`rm -fr $BUILDROOT/$PROJECT/document`;
	    $ret=`rm -fr $BUILDROOT/$PROJECT/documents`;
	    $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/document`;
	    $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/documents`;
	    $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/bootstrap*`;
	    $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/custom*`;
	    $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/multicompany*`;
	    $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/nltechno*`;
	    $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/pos*`;
	    $ret=`rm -fr $BUILDROOT/$PROJECT/test`;
	    $ret=`rm -fr $BUILDROOT/$PROJECT/Thumbs.db $BUILDROOT/$PROJECT/*/Thumbs.db $BUILDROOT/$PROJECT/*/*/Thumbs.db $BUILDROOT/$PROJECT/*/*/*/Thumbs.db $BUILDROOT/$PROJECT/*/*/*/*/Thumbs.db`;
	    $ret=`rm -f  $BUILDROOT/$PROJECT/.cvsignore $BUILDROOT/$PROJECT/*/.cvsignore $BUILDROOT/$PROJECT/*/*/.cvsignore $BUILDROOT/$PROJECT/*/*/*/.cvsignore $BUILDROOT/$PROJECT/*/*/*/*/.cvsignore $BUILDROOT/$PROJECT/*/*/*/*/*/.cvsignore $BUILDROOT/$PROJECT/*/*/*/*/*/*/.cvsignore`;
	    $ret=`rm -f  $BUILDROOT/$PROJECT/.gitignore $BUILDROOT/$PROJECT/*/.gitignore $BUILDROOT/$PROJECT/*/*/.gitignore $BUILDROOT/$PROJECT/*/*/*/.gitignore $BUILDROOT/$PROJECT/*/*/*/*/.gitignore $BUILDROOT/$PROJECT/*/*/*/*/*/.gitignore $BUILDROOT/$PROJECT/*/*/*/*/*/*/.gitignore`;
   	    $ret=`rm -f  $BUILDROOT/$PROJECT/htdocs/includes/geoip/sample*.*`;
        $ret=`rm -f  $BUILDROOT/$PROJECT/htdocs/includes/jquery/plugins/jqueryFileTree/connectors/jqueryFileTree.pl`;    # Avoid errors into rpmlint
        $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/includes/jquery/plugins/template`;  # Package not valid for most linux distributions (errors reported into compile.js). Package should be embed by modules to avoid problems.
        $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/includes/phpmailer`;                # Package not valid for most linux distributions (errors reported into file LICENSE). Package should be embed by modules to avoid problems.
   	    
	    $ret=`rm -f  $BUILDROOT/$PROJECT/htdocs/includes/jquery/plugins/multiselect/MIT-LICENSE.txt`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/includes/nusoap/lib/Mail`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/includes/phpexcel/license.txt`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/includes/phpexcel/PHPExcel/Shared/PDF`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/includes/phpexcel/PHPExcel/Shared/PCLZip`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/includes/tcpdf/fonts/dejavu-fonts-ttf-2.33`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/includes/tcpdf/fonts/freefont-20100919`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/includes/tcpdf/fonts/utils`;
	    $ret=`rm -f  $BUILDROOT/$PROJECT/htdocs/includes/tcpdf/LICENSE.TXT`;
        $ret=`rm -fr $BUILDROOT/$PROJECT/htdocs/includes/savant`;
	}

    # Build package for each target
    #------------------------------
    foreach my $target (keys %CHOOSEDTARGET) 
    {
        if ($CHOOSEDTARGET{$target} < 0) { next; }
    
        print "\nBuild package for target $target\n";

    	if ($target eq 'SNAPSHOT') 
    	{
    		$NEWDESTI=$DESTI;

    		print "Remove target $FILENAMESNAPSHOT.tgz...\n";
    		unlink("$NEWDESTI/$FILENAMESNAPSHOT.tgz");

            #rmdir "$BUILDROOT/$FILENAMESNAPSHOT";
    		$ret=`rm -fr $BUILDROOT/$FILENAMESNAPSHOT`;
            print "Copy $BUILDROOT/$PROJECT to $BUILDROOT/$FILENAMESNAPSHOT\n";
    		$cmd="cp -pr \"$BUILDROOT/$PROJECT\" \"$BUILDROOT/$FILENAMESNAPSHOT\"";
            $ret=`$cmd`;

    		print "Compress $BUILDROOT into $FILENAMESNAPSHOT.tgz...\n";
   		    #$cmd="tar --exclude \"$BUILDROOT/tgz/tar_exclude.txt\" --exclude .cache --exclude .settings --exclude conf.php --directory \"$BUILDROOT\" -czvf \"$FILENAMESNAPSHOT.tgz\" $FILENAMESNAPSHOT";
   		    $cmd="tar --exclude doli*.tgz --exclude doli*.deb --exclude doli*.exe --exclude doli*.xz --exclude doli*.zip --exclude doli*.rpm --exclude .cache --exclude .settings --exclude conf.php --exclude conf.php.mysql --exclude conf.php.old --exclude conf.php.postgres --directory \"$BUILDROOT\" --mode=go-w --group=500 --owner=500 -czvf \"$FILENAMESNAPSHOT.tgz\" $FILENAMESNAPSHOT";
			print $cmd."\n";
			$ret=`$cmd`;

    		# Move to final dir
       		print "Move $FILENAMESNAPSHOT.tgz to $NEWDESTI/$FILENAMESNAPSHOT.tgz\n";
       		$ret=`mv "$FILENAMESNAPSHOT.tgz" "$NEWDESTI/$FILENAMESNAPSHOT.tgz"`;
    		next;
    	}

    	if ($target eq 'TGZ') 
    	{
    		$NEWDESTI=$DESTI;
    		mkdir($DESTI.'/standard');
			if (-d $DESTI.'/standard') { $NEWDESTI=$DESTI.'/standard'; } 

    		print "Remove target $FILENAMETGZ.tgz...\n";
    		unlink("$NEWDESTI/$FILENAMETGZ.tgz");

            #rmdir "$BUILDROOT/$FILENAMETGZ";
    		$ret=`rm -fr $BUILDROOT/$FILENAMETGZ`;
            print "Copy $BUILDROOT/$PROJECT/ to $BUILDROOT/$FILENAMETGZ\n";
    		$cmd="cp -pr \"$BUILDROOT/$PROJECT/\" \"$BUILDROOT/$FILENAMETGZ\"";
            $ret=`$cmd`;

		    $ret=`rm -fr $BUILDROOT/$PROJECT/build/exe`;

    		print "Compress $FILENAMETGZ into $FILENAMETGZ.tgz...\n";
   		    $cmd="tar --exclude-vcs --exclude-from \"$BUILDROOT/$PROJECT/build/tgz/tar_exclude.txt\" --directory \"$BUILDROOT\" --mode=go-w --group=500 --owner=500 -czvf \"$FILENAMETGZ.tgz\" $FILENAMETGZ";
   		    $ret=`$cmd`;

    		# Move to final dir
       		print "Move $FILENAMETGZ.tgz to $NEWDESTI/$FILENAMETGZ.tgz\n";
       		$ret=`mv "$FILENAMETGZ.tgz" "$NEWDESTI/$FILENAMETGZ.tgz"`;
    		next;
    	}

    	if ($target eq 'XZ') 
    	{
    		$NEWDESTI=$DESTI;
    		mkdir($DESTI.'/standard');
			if (-d $DESTI.'/standard') { $NEWDESTI=$DESTI.'/standard'; } 

    		print "Remove target $FILENAMEXZ.xz...\n";
    		unlink("$NEWDESTI/$FILENAMEXZ.xz");

            #rmdir "$BUILDROOT/$FILENAMEXZ";
    		$ret=`rm -fr $BUILDROOT/$FILENAMEXZ`;
            print "Copy $BUILDROOT/$PROJECT to $BUILDROOT/$FILENAMEXZ\n";
    		$cmd="cp -pr \"$BUILDROOT/$PROJECT\" \"$BUILDROOT/$FILENAMEXZ\"";
            $ret=`$cmd`;

    		print "Compress $FILENAMEXZ into $FILENAMEXZ.xz...\n";
 
            print "Go to directory $BUILDROOT\n";
     		$olddir=getcwd();
     		chdir("$BUILDROOT");
    		$cmd= "xz -9 -r $BUILDROOT/$FILENAMEAPS.xz \*";
			print $cmd."\n";
			$ret= `$cmd`;
            chdir("$olddir");

    		# Move to final dir
            print "Move $FILENAMEXZ.xz to $NEWDESTI/$FILENAMEXZ.xz\n";
            $ret=`mv "$BUILDROOT/$FILENAMEXZ.xz" "$NEWDESTI/$FILENAMEXZ.xz"`;
    		next;
    	}
    	
    	if ($target eq 'ZIP') 
    	{
    		$NEWDESTI=$DESTI;
    		mkdir($DESTI.'/standard');
			if (-d $DESTI.'/standard') { $NEWDESTI=$DESTI.'/standard'; } 

    		print "Remove target $FILENAMEZIP.zip...\n";
    		unlink("$NEWDESTI/$FILENAMEZIP.zip");

            #rmdir "$BUILDROOT/$FILENAMEZIP";
    		$ret=`rm -fr $BUILDROOT/$FILENAMEZIP`;
            print "Copy $BUILDROOT/$PROJECT to $BUILDROOT/$FILENAMEZIP\n";
    		$cmd="cp -pr \"$BUILDROOT/$PROJECT\" \"$BUILDROOT/$FILENAMEZIP\"";
            $ret=`$cmd`;

    		print "Compress $FILENAMEZIP into $FILENAMEZIP.zip...\n";
 
            print "Go to directory $BUILDROOT\n";
     		$olddir=getcwd();
     		chdir("$BUILDROOT");
    		$cmd= "7z a -r -tzip -xr\@\"$BUILDROOT\/$FILENAMEZIP\/build\/zip\/zip_exclude.txt\" -mx $BUILDROOT/$FILENAMEZIP.zip $FILENAMEZIP\/*";
			print $cmd."\n";
			$ret= `$cmd`;
            chdir("$olddir");
            			
    		# Move to final dir
            print "Move $FILENAMEZIP.zip to $NEWDESTI/$FILENAMEZIP.zip\n";
            $ret=`mv "$BUILDROOT/$FILENAMEZIP.zip" "$NEWDESTI/$FILENAMEZIP.zip"`;
    		next;
    	}
    
    	if ($target =~ /RPM/)	                 # Linux only 
    	{
    		$NEWDESTI=$DESTI;
    		$subdir="package_rpm_generic";
    		if ($target =~ /FEDO/i) { $subdir="package_rpm_redhat-fedora"; }
    		if ($target =~ /MAND/i) { $subdir="package_rpm_mandriva"; }
    		if ($target =~ /OPEN/i) { $subdir="package_rpm_opensuse"; }
    		mkdir($DESTI.'/'.$subdir);
			if (-d $DESTI.'/'.$subdir) { $NEWDESTI=$DESTI.'/'.$subdir; } 

    		$ARCH='noarch';
			if ($RPMDIR eq "") { $RPMDIR=$ENV{'HOME'}."/rpmbuild"; }
           	$newbuild = $BUILD;
           	# For fedora
            $newbuild =~ s/(dev|alpha)/0.1.a/gi;			# dev
            $newbuild =~ s/beta/0.2.beta1/gi;				# beta
            $newbuild =~ s/rc./0.3.rc1/gi;					# rc
            if ($newbuild !~ /-/) { $newbuild.='-0.3'; }	# finale
            #$newbuild =~ s/(dev|alpha)/0/gi;				# dev
            #$newbuild =~ s/beta/1/gi;						# beta
            #$newbuild =~ s/rc./2/gi;						# rc
            #if ($newbuild !~ /-/) { $newbuild.='-3'; }		# finale
            #print "newbuild=".$newbuild."\n";exit;
            $REL1 = $newbuild; $REL1 =~ s/-.*$//gi;
            if ($RPMSUBVERSION eq 'auto') { $RPMSUBVERSION = $newbuild; $RPMSUBVERSION =~ s/^.*-//gi; }
            print "Version is $MAJOR.$MINOR.$REL1-$RPMSUBVERSION\n";

            $FILENAMETGZ2="$PROJECT-$MAJOR.$MINOR.$REL1";

            #print "Create directory $RPMDIR\n";
            #$ret=`mkdir -p "$RPMDIR"`;

    		print "Remove target ".$FILENAMETGZ2."-".$RPMSUBVERSION.".".$ARCH.".rpm...\n";
    		unlink("$NEWDESTI/".$FILENAMETGZ2."-".$RPMSUBVERSION.".".$ARCH.".rpm");
    		print "Remove target ".$FILENAMETGZ2."-".$RPMSUBVERSION.".src.rpm...\n";
    		unlink("$NEWDESTI/".$FILENAMETGZ2."-".$RPMSUBVERSION.".src.rpm");

    		print "Create directory $BUILDROOT/$FILENAMETGZ2\n";
    		$ret=`rm -fr $BUILDROOT/$FILENAMETGZ2`;
            print "Copy $BUILDROOT/$PROJECT to $BUILDROOT/$FILENAMETGZ2\n";
    		$cmd="cp -pr '$BUILDROOT/$PROJECT' '$BUILDROOT/$FILENAMETGZ2'";
            $ret=`$cmd`;

			# Set owners
            print "Set owners on files/dir\n";
		    $ret=`chown -R root.root $BUILDROOT/$FILENAMETGZ2`;

            print "Set permissions on files/dir\n";
		    $ret=`chmod -R 755 $BUILDROOT/$FILENAMETGZ2`;
		    $cmd="find $BUILDROOT/$FILENAMETGZ2 -type f -exec chmod 644 {} \\; ";
            $ret=`$cmd`;

			# Build tgz
    		print "Compress $FILENAMETGZ2 into $FILENAMETGZ2.tgz...\n";
    		$ret=`tar --exclude-from "$SOURCEDOL/build/tgz/tar_exclude.txt" --directory "$BUILDROOT" -czvf "$BUILDROOT/$FILENAMETGZ2.tgz" $FILENAMETGZ2`;

    		print "Move $BUILDROOT/$FILENAMETGZ2.tgz to $RPMDIR/SOURCES/$FILENAMETGZ2.tgz\n";
    		$cmd="mv $BUILDROOT/$FILENAMETGZ2.tgz $RPMDIR/SOURCES/$FILENAMETGZ2.tgz";
            $ret=`$cmd`;

    		$BUILDFIC="${FILENAME}.spec";
    		$BUILDFICSRC="${FILENAME}_generic.spec";
    		if ($target =~ /FEDO/i) { $BUILDFICSRC="${FILENAME}_fedora.spec"; }
    		if ($target =~ /MAND/i) { $BUILDFICSRC="${FILENAME}_mandriva.spec"; }
    		if ($target =~ /OPEN/i) { $BUILDFICSRC="${FILENAME}_opensuse.spec"; }
    		
 			print "Generate file $BUILDROOT/$BUILDFIC from $SOURCEDOL/build/rpm/${BUILDFICSRC}\n";
            open (SPECFROM,"<$SOURCEDOL/build/rpm/${BUILDFICSRC}") || die "Error";
            open (SPECTO,">$BUILDROOT/$BUILDFIC") || die "Error";
            while (<SPECFROM>) {
                $_ =~ s/__FILENAMETGZ__/$FILENAMETGZ/;
                $_ =~ s/__VERSION__/$MAJOR.$MINOR.$REL1/;
                $_ =~ s/__RELEASE__/$RPMSUBVERSION/;
                print SPECTO $_;
            }
            close SPECFROM;
            close SPECTO;
    
    		print "Copy patch file to $RPMDIR/SOURCES\n";
    		$ret=`cp "$SOURCEDOL/build/rpm/dolibarr-forrpm.patch" "$RPMDIR/SOURCES"`;
		    $ret=`chmod 644 $RPMDIR/SOURCES/dolibarr-forrpm.patch`;

    		print "Launch RPM build (rpmbuild --clean -ba $BUILDROOT/${BUILDFIC})\n";
    		#$ret=`rpmbuild -vvvv --clean -ba $BUILDROOT/${BUILDFIC}`;
    		$ret=`rpmbuild --clean -ba $BUILDROOT/${BUILDFIC}`;

    		# Move to final dir
   		    print "Move $RPMDIR/RPMS/".$ARCH."/".$FILENAMETGZ2."-".$RPMSUBVERSION."*.".$ARCH.".rpm into $NEWDESTI/".$FILENAMETGZ2."-".$RPMSUBVERSION."*.".$ARCH.".rpm\n";
   		    #$cmd="mv \"$RPMDIR/RPMS/".$ARCH."/".$FILENAMETGZ2."-".$RPMSUBVERSION.".".$ARCH.".rpm\" \"$NEWDESTI/".$FILENAMETGZ2."-".$RPMSUBVERSION.".".$ARCH.".rpm\"";
   		    $cmd="mv $RPMDIR/RPMS/".$ARCH."/".$FILENAMETGZ2."-".$RPMSUBVERSION."*.".$ARCH.".rpm \"$NEWDESTI/\"";
    		$ret=`$cmd`;
   		    print "Move $RPMDIR/SRPMS/".$FILENAMETGZ2."-".$RPMSUBVERSION."*.src.rpm into $NEWDESTI/".$FILENAMETGZ2."-".$RPMSUBVERSION."*.src.rpm\n";
   		    #$cmd="mv \"$RPMDIR/SRPMS/".$FILENAMETGZ2."-".$RPMSUBVERSION.".src.rpm\" \"$NEWDESTI/".$FILENAMETGZ2."-".$RPMSUBVERSION.".src.rpm\"";
   		    $cmd="mv $RPMDIR/SRPMS/".$FILENAMETGZ2."-".$RPMSUBVERSION."*.src.rpm \"$NEWDESTI/\"";
    		$ret=`$cmd`;
   		    print "Move $RPMDIR/SOURCES/".$FILENAMETGZ2.".tgz into $NEWDESTI/".$FILENAMETGZ2.".tgz\n";
   		    $cmd="mv \"$RPMDIR/SOURCES/".$FILENAMETGZ2.".tgz\" \"$NEWDESTI/".$FILENAMETGZ2.".tgz\"";
    		$ret=`$cmd`;
    		next;
    	}

    	if ($target eq 'DEB') 
    	{
    		$NEWDESTI=$DESTI;
    		mkdir($DESTI.'/package_debian-ubuntu');
			if (-d $DESTI.'/package_debian-ubuntu') { $NEWDESTI=$DESTI.'/package_debian-ubuntu'; } 

            $olddir=getcwd();

            $newbuild = $BUILD;
            $newbuild =~ s/(dev|alpha)/1/gi;                # dev
            $newbuild =~ s/beta/2/gi;                       # beta
            $newbuild =~ s/rc./3/gi;                        # rc
            if ($newbuild !~ /-/) { $newbuild.='-4'; }      # finale
            # now newbuild is 0-1 or 0-4 for example
            print "Version is $MAJOR.$MINOR.$newbuild\n";
            $build = $newbuild;
            $build =~ s/-.*$//g;
			# now build is 0 for example
			# $build .= '+nmu1';
			# now build is 0+nmu1 for example
			
    		print "Remove target ${FILENAMEDEB}_all.deb...\n";
    		unlink("$NEWDESTI/${FILENAMEDEB}_all.deb");
    		print "Remove target ${FILENAMEDEB}.dsc...\n";
    		unlink("$NEWDESTI/${FILENAMEDEB}.dsc");
    		print "Remove target ${FILENAMEDEB}.tar.gz...\n";
    		unlink("$NEWDESTI/${FILENAMEDEB}.tar.gz");
    		print "Remove target ${FILENAMEDEB}.changes...\n";
    		unlink("$NEWDESTI/${FILENAMEDEB}.changes");

    		$ret=`rm -fr $BUILDROOT/$PROJECT.tmp`;
    		$ret=`rm -fr $BUILDROOT/$PROJECT-$MAJOR.$MINOR.$build`;
    		
			print "Copy $BUILDROOT/$PROJECT to $BUILDROOT/$PROJECT.tmp\n";
			$cmd="cp -pr \"$BUILDROOT/$PROJECT\" \"$BUILDROOT/$PROJECT.tmp\"";
			$ret=`$cmd`;
			$cmd="cp -pr \"$BUILDROOT/$PROJECT/build/debian/apache/.htaccess\" \"$BUILDROOT/$PROJECT.tmp/build/debian/apache/.htaccess\"";
			$ret=`$cmd`;

 			print "Remove other files\n";
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/README-FR`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/README`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/README-FR`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/build/aps`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/build/dmg`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/pad/README`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/tgz/README`;
            #$ret=`rm -fr $BUILDROOT/$PROJECT.tmp/build/debian`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/build/debian/po`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/build/debian/source`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/debian/changelog`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/debian/compat`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/debian/control*`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/debian/copyright`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/debian/dolibarr.config`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/debian/dolibarr.desktop`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/debian/dolibarr.doc-base`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/debian/dolibarr.install`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/debian/dolibarr.lintian-overrides`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/debian/dolibarr.postrm`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/debian/dolibarr.postinst`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/debian/dolibarr.templates`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/debian/dolibarr.templates.futur`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/debian/rules`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/debian/README.howto`;
            $ret=`rm -f  $BUILDROOT/$PROJECT.tmp/build/debian/wash`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/build/doap`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/build/exe`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/build/launchpad`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/build/live`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/build/patch`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/build/perl`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/build/rpm`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/build/zip`;
            # We remove embedded libraries or fonts (this is also inside rules file, target clean)
	   	    $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/htdocs/includes/ckeditor`;
			$ret=`rm -fr $BUILDROOT/$PROJECT.tmp/htdocs/includes/fonts`,
	   	    $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/htdocs/includes/geoip`;
	   	    $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/htdocs/includes/nusoap`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/htdocs/includes/odtphp/zip/pclzip`;

            # Prepare source package (init debian dir)
            print "Create directory $BUILDROOT/$PROJECT.tmp/debian\n";
            $ret=`mkdir "$BUILDROOT/$PROJECT.tmp/debian"`;
            print "Copy $SOURCEDOL/build/debian/xxx to $BUILDROOT/$PROJECT.tmp/debian\n";
            # Add files for dpkg-source
            $ret=`cp -f  "$SOURCEDOL/build/debian/changelog"      "$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -f  "$SOURCEDOL/build/debian/compat"         "$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -f  "$SOURCEDOL/build/debian/control"        "$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -f  "$SOURCEDOL/build/debian/copyright"      "$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -f  "$SOURCEDOL/build/debian/dolibarr.desktop"        	"$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -f  "$SOURCEDOL/build/debian/dolibarr.doc-base"        	"$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -f  "$SOURCEDOL/build/debian/dolibarr.install" 	        "$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -f  "$SOURCEDOL/build/debian/dolibarr.lintian-overrides"  "$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -f  "$SOURCEDOL/build/debian/dolibarr.xpm"  		      	"$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -f  "$SOURCEDOL/build/debian/README.source"  "$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -f  "$SOURCEDOL/build/debian/rules"          "$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -fr "$SOURCEDOL/build/debian/patches"        "$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -fr "$SOURCEDOL/build/debian/po"             "$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -fr "$SOURCEDOL/build/debian/source"         "$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -fr "$SOURCEDOL/build/debian/apache"         "$BUILDROOT/$PROJECT.tmp/debian/apache"`;
            $ret=`cp -f  "$SOURCEDOL/build/debian/apache/.htaccess" "$BUILDROOT/$PROJECT.tmp/debian/apache"`;
            $ret=`cp -fr "$SOURCEDOL/build/debian/lighttpd"       "$BUILDROOT/$PROJECT.tmp/debian/lighttpd"`;
            # Add files also required to build binary package
            $ret=`cp -f  "$SOURCEDOL/build/debian/dolibarr.config"         "$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -f  "$SOURCEDOL/build/debian/dolibarr.postinst"       "$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -f  "$SOURCEDOL/build/debian/dolibarr.postrm"         "$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -f  "$SOURCEDOL/build/debian/dolibarr.templates"      "$BUILDROOT/$PROJECT.tmp/debian"`;
            $ret=`cp -f  "$SOURCEDOL/build/debian/install.forced.php.install"      "$BUILDROOT/$PROJECT.tmp/debian"`;
            
			# Set owners and permissions
            print "Set owners on files/dir\n";
		    $ret=`chown -R root.root $BUILDROOT/$PROJECT.tmp`;
            print "Set permissions on files/dir\n";
		    $ret=`chmod -R 755 $BUILDROOT/$PROJECT.tmp`;
		    $cmd="find $BUILDROOT/$PROJECT.tmp -type f -exec chmod 644 {} \\; ";
            $ret=`$cmd`;
            $cmd="find $BUILDROOT/$PROJECT.tmp/build -name '*.php' -type f -exec chmod 755 {} \\; ";
            $ret=`$cmd`;
            $cmd="find $BUILDROOT/$PROJECT.tmp/build -name '*.dpatch' -type f -exec chmod 755 {} \\; ";
            $ret=`$cmd`;
            $cmd="find $BUILDROOT/$PROJECT.tmp/build -name '*.pl' -type f -exec chmod 755 {} \\; ";
            $ret=`$cmd`;
            $cmd="find $BUILDROOT/$PROJECT.tmp/dev -name '*.php' -type f -exec chmod 755 {} \\; ";
            $ret=`$cmd`;
            $ret=`chmod 755 $BUILDROOT/$PROJECT.tmp/debian/rules`;
            $ret=`chmod -R 644 $BUILDROOT/$PROJECT.tmp/dev/translation/autotranslator.class.php`;
            $ret=`chmod -R 644 $BUILDROOT/$PROJECT.tmp/dev/skeletons/modMyModule.class.php`;
            $ret=`chmod -R 644 $BUILDROOT/$PROJECT.tmp/dev/skeletons/skeleton_class.class.php`;
            $ret=`chmod -R 644 $BUILDROOT/$PROJECT.tmp/dev/skeletons/skeleton_page.php`;
            $ret=`chmod -R 644 $BUILDROOT/$PROJECT.tmp/dev/skeletons/skeleton_webservice_server.php`;
            $cmd="find $BUILDROOT/$PROJECT.tmp/scripts -name '*.php' -type f -exec chmod 755 {} \\; ";
            $ret=`$cmd`;
            $cmd="find $BUILDROOT/$PROJECT.tmp/scripts -name '*.sh' -type f -exec chmod 755 {} \\; ";
            $ret=`$cmd`;
            
          
            print "Rename directory $BUILDROOT/$PROJECT.tmp into $BUILDROOT/$PROJECT-$MAJOR.$MINOR.$build\n";
            $cmd="mv $BUILDROOT/$PROJECT.tmp $BUILDROOT/$PROJECT-$MAJOR.$MINOR.$build";
            $ret=`$cmd`;

			# Creation of source package          
     		print "Go into directory $BUILDROOT/$PROJECT-$MAJOR.$MINOR.$build\n";
            chdir("$BUILDROOT/$PROJECT-$MAJOR.$MINOR.$build");
            #$cmd="dpkg-source -b $BUILDROOT/$PROJECT-$MAJOR.$MINOR.$build";
            $cmd="dpkg-buildpackage -us -uc";
            print "Launch DEB build ($cmd)\n";
            $ret=`$cmd 2>&1 3>&1`;
            print $ret."\n";

            chdir("$olddir");
    		
    		# Move to final dir
            print "Move *_all.deb to $NEWDESTI\n";
            $ret=`mv $BUILDROOT/*_all.deb "$NEWDESTI/"`;
            $ret=`mv $BUILDROOT/*.dsc "$NEWDESTI/"`;
            $ret=`mv $BUILDROOT/*.tar.gz "$NEWDESTI/"`;
            $ret=`mv $BUILDROOT/*.changes "$NEWDESTI/"`;
        	next;
        }
        
    	if ($target eq 'APS') 
    	{
			$NEWDESTI=$DESTI;
    		mkdir($DESTI.'/package_aps');
			if (-d $DESTI.'/package_aps') { $NEWDESTI=$DESTI.'/package_aps'; } 
			
            $newbuild = $BUILD;
            $newbuild =~ s/(dev|alpha)/0/gi;                # dev
            $newbuild =~ s/beta/1/gi;                       # beta
            $newbuild =~ s/rc./2/gi;                        # rc
            if ($newbuild !~ /-/) { $newbuild.='-3'; }      # finale
            # now newbuild is 0-0 or 0-3 for example
            $REL1 = $newbuild; $REL1 =~ s/-.*$//gi;
            if ($RPMSUBVERSION eq 'auto') { $RPMSUBVERSION = $newbuild; $RPMSUBVERSION =~ s/^.*-//gi; }
            print "Version is $MAJOR.$MINOR.$REL1-$RPMSUBVERSION\n";
    		
     		print "Remove target $FILENAMEAPS.zip...\n";
    		unlink "$NEWDESTI/$FILENAMEAPS.zip";
 
            #rmdir "$BUILDROOT/$PROJECT.tmp";
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp`;
            print "Create directory $BUILDROOT/$PROJECT.tmp\n";
            $ret=`mkdir -p "$BUILDROOT/$PROJECT.tmp"`;
            print "Copy $BUILDROOT/$PROJECT to $BUILDROOT/$PROJECT.tmp\n";
            $cmd="cp -pr \"$BUILDROOT/$PROJECT\" \"$BUILDROOT/$PROJECT.tmp\"";
            $ret=`$cmd`;

            print "Remove other files\n";
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/$PROJECT/build/deb`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/$PROJECT/build/dmg`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/$PROJECT/build/doap`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/$PROJECT/build/exe`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/$PROJECT/build/live`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/$PROJECT/build/patch`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/$PROJECT/build/rpm`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/$PROJECT/build/zip`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/$PROJECT/build/perl`;

            $APSVERSION="1.2";
            print "Create APS files $BUILDROOT/$PROJECT.tmp/$PROJECT/APP-META.xml\n";
            open (SPECFROM,"<$BUILDROOT/$PROJECT/build/aps/APP-META-$APSVERSION.xml") || die "Error";
            open (SPECTO,">$BUILDROOT/$PROJECT.tmp/$PROJECT/APP-META.xml") || die "Error";
            while (<SPECFROM>) {
                $newbuild = $BUILD;
                $newbuild =~ s/(dev|alpha)/0/gi;                # dev
                $newbuild =~ s/beta/1/gi;                       # beta
                $newbuild =~ s/rc./2/gi;                        # rc
                if ($newbuild !~ /-/) { $newbuild.='-3'; }      # finale
                # now newbuild is 0-0 or 0-3 for example
                $_ =~ s/__VERSION__/$MAJOR.$MINOR.$REL1/;
                $_ =~ s/__RELEASE__/$RPMSUBVERSION/;
                print SPECTO $_;
            }
            close SPECFROM;
            close SPECTO;
            print "Version set to $MAJOR.$MINOR.$newbuild\n";
            #$cmd="cp -pr \"$BUILDROOT/$PROJECT/build/aps/configure\" \"$BUILDROOT/$PROJECT.tmp/$PROJECT/scripts/configure\"";
            #$ret=`$cmd`;
            $cmd="cp -pr \"$BUILDROOT/$PROJECT/build/aps/configure.php\" \"$BUILDROOT/$PROJECT.tmp/$PROJECT/scripts/configure.php\"";
            $ret=`$cmd`;
            $cmd="cp -pr \"$BUILDROOT/$PROJECT/doc/images\" \"$BUILDROOT/$PROJECT.tmp/$PROJECT/images\"";
            $ret=`$cmd`;
 
            print "Remove other files\n";
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/$PROJECT/dev`;
            $ret=`rm -fr $BUILDROOT/$PROJECT.tmp/$PROJECT/doc`;
            
            print "Build APP-LIST.xml files\n";
            
            print "Compress $BUILDROOT/$PROJECT.tmp/$PROJECT into $FILENAMEAPS.zip...\n";
 
            print "Go to directory $BUILDROOT/$PROJECT.tmp\/$PROJECT\n";
            $olddir=getcwd();
            chdir("$BUILDROOT\/$PROJECT.tmp\/$PROJECT");
            $cmd= "zip -9 -r $BUILDROOT/$FILENAMEAPS.zip \*";
            print $cmd."\n";
            $ret= `$cmd`;
            chdir("$olddir");
                        
    		# Move to final dir
            print "Move $BUILDROOT/$FILENAMEAPS.zip to $NEWDESTI/$FILENAMEAPS.zip\n";
            $ret=`mv "$BUILDROOT/$FILENAMEAPS.zip" "$NEWDESTI/$FILENAMEAPS.zip"`;
            next;
    	}

    	if ($target eq 'EXEDOLIWAMP')
    	{
    		$NEWDESTI=$DESTI;
    		mkdir($DESTI.'/package_windows');
			if (-d $DESTI.'/package_windows') { $NEWDESTI=$DESTI.'/package_windows'; } 
    		
     		print "Remove target $FILENAMEEXEDOLIWAMP.exe...\n";
    		unlink "$NEWDESTI/$FILENAMEEXEDOLIWAMP.exe";
 
 			$SOURCEBACK=$SOURCEMOD;
 			$SOURCEBACK =~ s/\//\\/g;
    		print "Compil exe $FILENAMEEXEDOLIWAMP.exe file from iss file \"\\tmp\\buildroot\\build\\exe\\dolimed\\dolimed.iss\"\n";
    		$cmd= "ISCC.exe \"\\tmp\\buildroot\\$PROJECT\\build\\exe\\dolimed\\dolimed.iss\"";
			print "$cmd\n";
			$ret= `$cmd`;
			#print "$ret\n";

    		# Move to final dir
			print "Move $BUILDROOT/$PROJECT/build/$FILENAMEEXEDOLIWAMP.exe to $NEWDESTI/$FILENAMEEXEDOLIWAMP.exe\n";
    		rename("$BUILDROOT/$PROJECT/build/$FILENAMEEXEDOLIWAMP.exe","$NEWDESTI/$FILENAMEEXEDOLIWAMP.exe");
            print "Move $BUILDROOT/$PROJECT/build/$FILENAMEEXEDOLIWAMP.exe to $NEWDESTI/$FILENAMEEXEDOLIWAMP.exe\n";
            $ret=`mv "$BUILDROOT/$PROJECT/build/$FILENAMEEXEDOLIWAMP.exe" "$NEWDESTI/$FILENAMEEXEDOLIWAMP.exe"`;
    		next;
    	}
    }
}

print "\n----- Summary -----\n";
foreach my $target (keys %CHOOSEDTARGET) {
    if ($CHOOSEDTARGET{$target} < 0) {
        print "Package $target not built (bad requirement).\n";
    } else {
        print "Package $target built successfully in $DESTI\n";
    }
}

if (! $batch) {
    print "\nPress key to finish...";
    my $WAITKEY=<STDIN>;
}

0;
