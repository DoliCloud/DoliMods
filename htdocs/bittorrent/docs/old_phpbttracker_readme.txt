Welcome to my BitTorrent Tracker written in PHP.

Highlights:
+ Provides the same functionality as the offical tracker
+ Runs using MySQL as a database backend
+ Built-in statistics collection with sample summary script
+ Customiztion is pretty easy to implement

Pitfalls
- PHP has some limitations, so this tracker is not optimal.

This is my first PHP project, and I'm rather happy with the
result.

UPGRADING
---------

If you are upgrading from a previous version, then you may be in trouble.
The database structure was slightly modified to accomidate a change in the
latest MySQL. The word "hash" became a keyword, and cannot be used as a
column name. Furthermore, the addition of the "speed" code requires
table additions and a new table entirely.

The script upgrade.php is provided to carry out these modifications. You do
not need to run it if you are installing from scratch, and if only needs
to be done once regardless. Also, running it will not cause any problems
even if you have the latest version of the database.


** New in version 1.5: Peer caching. If you want to use this feature,
you must execute the makecache.php script to generate the tables from
your current database.


INSTALLATION
------------

Requirements:
- Working PHP environment (ideally Apache with PHP built-in or
  working via module)
- Working MySQL server


Upload tracker.php, funcsv2.php, newtorrents.php, BDecode.php, BEncode.php
 and install.php to the web site which will be hosting the tracker. Uploading 
index.php is recommended if you want a home page for the tracker. Feel free
to re-theme it.

Access the install.php script from your web browser. It will
guide you through the creation of the SQL database. All you need
is the database's username and password. You may want to let your
webmaster run through this phase.

If install.php has write access to the installation directory, it will
write its own config.php file with the database configuration and some
default settings. If install.php cannot do this, you must modify
config-sample.php yourself and upload it to the same directory as
tracker.php and rename it to config.php.

*************************
************************* Set up config.php !!!

There are two variables named $upload_username and $upload_password.
These are the values that will be used by the newtorrents.php script
to authorize submission of new torrents. You must set these, or your
tracker will not accept new files, making it rather useless.



OTHER FILES
-----------

The tracker package also includes some other scripts. Here is a list and a
description of what they do.

- DumpTorrentCGI.php
 Originally intended as a demo of the BEncode library, but it became popular
 pretty quickly. This script allows users to upload a .torrent file to the
 server (or specify a URL to download) and the script will decode it and
 display the file's contents to the user in a (hopefully) friendly manner.
 It also supports other bencoded data, such as /announce and /scrape data,
 although it is not reliable enough to do /scrape due to a strange quirk
 in PHP.
- BEncode.php
 Used by DumpTorrentCGI.php and newtorrents.php to make bencoded data
 streams. The primary reason for doing this is calculating info_hash values.
- BDecode.php
 The decoding compliment to BEncode.php
- sanity.php
 When run, this script will do some simple consistency checks on the
 tracker's summary page and will forcibly expire peers who have not reported
 in within double the configured re-announce interval. If it doesn't seem to
 work, try running it as sanity.php?nolock=on
- sha1lib.php
 An SHA1 implementation entirely in PHP. It's not perfect and it's slow, but
 in a pinch, it works fine. Ignored if PHP version is at least 4.3.0 or if
 the mhash extension is installed.


The rest is documentation and other text documents.

FILE RENAMING AND MOVING
------------------------
All PHP files will function properly if renamed, except
funcsv2.php and config.php. Renaming these files require
modifying most other .php files.


USAGE
-----
Create your torrent files as usual. Specify the url to 
tracker.php (or its new name if you renamed it) as the announce
URL.

***********************************
If you want /scrape functionality, target announce.php
instead of tracker.php.
***********************************

Call up the newtorrents.php URL. Specify all the data you want to
show up on the statistics page. You must specify at least the
username, password, and either upload the .torrent or copy the
info_hash into the indicated field.

The checkbox, when checked (defaults to yes) will cause the script
to fill in the file's name and a short description. The description is
the file's size (roughly calculated) and the comment field if present
in the torrent file.

*New: a PHP implemention of the SHA1 algorithm is included. All users
can upload directly to the newtorrents.php script now. Note however
that is may produce wrong hashes and generally run slowly.


DELETING TORRENTS
-----------------
The script deleter.php allows you to delete torrents from the database.
The username and password are NOT the same as newtorrnets.php uses.
Use the login and password that the SQL database itself uses. Of course,
there's nothing preventing you from making these identical.

Be warned: there is no confirmation of deletion and a torrent
need not be abandoned to be erased. Changes take effect immediately.



TORRENTSPY COMPATABILITY (and other /scrape functions)
------------------------
Starting with version 1.5, since an official statement has
been made on /scrape conventions, announce.php and scrape.php
are provided. announce.php simply executes tracker.php,
while scrape.php causes tracker.php to output scrape data.

The old style of using http://www.site.com/tracker.php/announce
is still included (in fact, this is how scrape.php works) but
is now discouraged since this convention caused more problems
than it ever really should have.

Any program not capable of figuring out the scrape.php script
name from announce.php is broken and needs to be fixed.



STATISTIC COLLECTING (or, "Database Structure")
--------------------

I tried to make the database information as easy to understand
as possible. "SELECT * FROM summary" should provide you with
all the programming information you need, but here is a brief
rundown of what the fields mean.

Summary:
	*info_hash - The 40 character hex representation of
	 the file. It is unique to every torrent.

	*dlbytes - The approximate sum of all the bytes downloaded
	 by everyone.

	*seeds - The number of connected users who have the
	 whole file and are uploading.

	*leechers - The number of connected users who are still
	 downloading the file.

	*finished - The number of users who have fully downloaded
	 the file. Use this as a measure of how many people
	 have the file.

	*lastcycle - Used by the trash collector to decide if
	 it should try to purge users who have timed out.

	*lastSpeedCycle - Used by the speec calculator to decide
	 if the speed should be updated.

	*speed - in bytes per second. Consider it to be extremely
	rough.

Namemap:
  Note that all fields (except hash) are optional and may be "" (but not NULL,
  those are annoying). A torrent need not have an entry here at all.
  This is used only by the index.php script.
	*info_hash - The file's unique 40 character hash.

	*filename - The file that this torrent represents

	*url - A link to where the .torrent file may be grabbed.

	*info - A short text description added after the previous
	 information is shown. Default is the file size.

timestamps:
  Used by the speed calculator to contain the sliding window average
  download rate. This is of little interest to external users, so 
  I'll skip it.

x<hexadecimal string>:
  Each torrent's user list is stored in a table whose name is the
  info_hash of the torrent prefixed by an x.  
  
	*peer_id - A 40 character hash that is unique to each client

	*bytes - The number of bytes this peer still needs to download
	 to have the complete file. Seeders have this set to 0.

	*ip - The client's IP address

	*port - The port the client is listening on (usually 6881)

	*status - Either "seeder" or "leecher" (see above). It's a bit
	 redundent right now since "bytes==0" is the same as a seeder	 

	*lastupdate - Unix time of when the client last reported in.
	 Clients whose time is 2 * report_interval will be deleted.

y<hexadecimal string>:
  The couterpart to the "x" table, only with the peer caching data.
  I won't describe it here.


CREDITS
-------

People besides me who deserve credit.

Bram Cohen - Author of BT, and really patient guy.
KktoMx     - Figured out the "stripslashes" problem.
bideomex   - Found the dumb thing I did with stripslashes.
Gottaname  - First real load test.
"daan" (?) - SHA1 in PHP code. See http://www.php.net/manual/en/function.sha1.php
             user comments.
Bak4San    - Provider of torrents with ten thousand peers. On a weekly
             basis.
