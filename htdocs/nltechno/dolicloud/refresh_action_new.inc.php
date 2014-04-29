<?php


// Do actions on object $object


// Avoid errors onto ssh2 and stats function warning
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

$langs->load("errors");

if ($action == 'addauthorizedkey')
{
	// SFTP connect
	if (! function_exists("ssh2_connect")) {
		dol_print_error('','ssh2_connect function does not exists'); exit;
	}

	$server=$object->instance.'.on.dolicloud.com';
	$connection = ssh2_connect($server, 22);
	if ($connection)
	{
		//print $object->instance." ".$object->username_web." ".$object->password_web."<br>\n";
		if (! @ssh2_auth_password($connection, $object->username_web, $object->password_web))
		{
			dol_syslog("Could not authenticate with username ".$username." . and password ".$password,LOG_ERR);
		}
		else
		{
			$sftp = ssh2_sftp($connection);

			// Update ssl certificate
			// Dir .ssh must have rwx------ permissions
			// File authorized_keys must have rw------- permissions
			$dircreated=0;
			$result=ssh2_sftp_mkdir($sftp, $conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/.ssh');
			if ($result) {
				$dircreated=1;
			}	// Created
			else {
				$dircreated=0;
			}	// Creation fails or already exists

			// Check if authorized_key exists
			$filecert="ssh2.sftp://".$sftp.$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/.ssh/authorized_keys';
			$fstat=stat($filecert);

			// Create authorized_keys file
			if (empty($fstat['atime']))		// Failed to connect or file does not exists
			{
				$stream = fopen($filecert, 'w');
				if ($stream === false)
				{
					setEventMessage($langs->transnoentitiesnoconv("ErrorConnectOkButFailedToCreateFile"),'errors');
				}
				else
				{
					fwrite($stream,"ssh-dss AAAAB3NzaC1kc3MAAACBAKu0WcYS8t02uoInHqyxKxQ7qOJaoOw1bRPPSzEKeXZcdHcBffEHpgLUTYEuk8x6rviQ0yRp960NyrjZNCe1rn5cXWuZpJQe/dBGuVMdSK0LiCr6xar66XOsuDDssZn3w0u97pId8wMrsYBzFUj/J3XSbAf5gX5MfWiUuPG+ZcyPAAAAFQCnXg8nISCy6fs11Lo0UXH4fUuSCwAAAIB5TqwLW4lrA0GavA/HG4sS3BdRE8ZxgKRkqY/LQGmVT7MOTCpae97YT7vA8AkPFOpVZWX9qpYD1EjvJlcB9PASmROSV1JCwxXsEK0vxc+MsogqNJTYifdonEjQJJ8dLKh0KPkXoBrTJnn7xNzdarukbiYPDNvH2/OaXUdkrrUoFwAAAIACief5fwRcSeS3R3uTIyoVUBJGhjtOxkEnS6kMvXpdrLi6nMGQvAxsusVhT60gZNHZpOd8zbs0RWI6hBttZl+zd2yK16PFzLbZYR//sQW0vrV4662KbkcgclYNATbVzrZjPUi6LeJ+1PA/n0pI4leWhD+w7hWEPWEkGVGBrwKFAA== admin@apollon1.nltechno.com\nssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAIEAp6Nj1j5jVgziTIRPiWIdqm95P+yT5wAFYzzyzy5g1/ip+YRz6DT+TJUnpI3+coKPtTGahFkHRUIxCMBBObbgkpw0wJr9aBJrZ4YNSIe+DdmIe0JU4L40eHtOcxDNRFCeS8n9LaQ3/K+UV6JEhplibLYEhPKPn4fTfm7Krj0KDVc= admin@apollon1.nltechno.com\n");
					fclose($stream);
					$fstat=stat($filecert);
					setEventMessage($langs->transnoentitiesnoconv("FileCreated"),'mesgs');
				}
			}
			else setEventMessage($langs->transnoentitiesnoconv("ErrorFileAlreadyExists"),'warnings');

			$object->fileauthorizedkey=(empty($fstat['atime'])?'':$fstat['atime']);

			if (! empty($fstat['atime'])) $result = $object->update($user);
		}
	}
	else setEventMessage($langs->transnoentitiesnoconv("FailedToConnectToSftp"),'errors');
}


if ($action == 'disable_instance')
{
	// We push a page to disable instance

	// SFTP connect
	if (! function_exists("ssh2_connect")) {
		dol_print_error('','ssh2_connect function does not exists'); exit;
	}

	$server=$object->instance.'.on.dolicloud.com';
	$connection = ssh2_connect($server, 22);
	if ($connection)
	{
		//print $object->instance." ".$object->username_web." ".$object->password_web."<br>\n";
		if (! @ssh2_auth_password($connection, $object->username_web, $object->password_web))
		{
			dol_syslog("Could not authenticate with username ".$username." . and password ".$password,LOG_ERR);
		}
		else
		{
			$sftp = ssh2_sftp($connection);

			// Check if install.lock exists
			$dir=preg_replace('/_([a-zA-Z0-9]+)$/','',$object->database_db);
			$filedisabled="ssh2.sftp://".$sftp.$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.$dir.'/htdocs/index.html';
			$fstat=stat($filedisabled);
			if (empty($fstat['atime']))
			{
				$stream = fopen($filedisabled, 'w');
				//var_dump($stream);exit;
				$filesource=file_get_contents('index_disabled_en_US.html');
				$filesource=preg_replace('/__instance__/', $object->instance, $filesource);
				fwrite($stream,$filesource);
				fclose($stream);
				$fstat=stat($filedisabled);
				setEventMessage($langs->transnoentitiesnoconv("FileToDisableInstanceCreated",$object->instance),'warnings');
			}
			else setEventMessage($langs->transnoentitiesnoconv("ErrorFileAlreadyExists"),'warnings');
		}
	}
}


if ($action == 'enable_instance')
{
	// SFTP connect
	if (! function_exists("ssh2_connect")) {
		dol_print_error('','ssh2_connect function does not exists'); exit;
	}

	$server=$object->instance.'.on.dolicloud.com';
	$connection = ssh2_connect($server, 22);
	if ($connection)
	{
		//print $object->instance." ".$object->username_web." ".$object->password_web."<br>\n";
		if (! @ssh2_auth_password($connection, $object->username_web, $object->password_web))
		{
			dol_syslog("Could not authenticate with username ".$username." . and password ".$password,LOG_ERR);
		}
		else
		{
			$sftp = ssh2_sftp($connection);

			// Check if install.lock exists
			$dir=preg_replace('/_([a-zA-Z0-9]+)$/','',$object->database_db);
			$filetodelete=$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.$dir.'/htdocs/index.html';
			$result=ssh2_sftp_unlink($sftp, $filetodelete);

			if ($result) setEventMessage($langs->transnoentitiesnoconv("FileToDisableInstanceRemoved",$object->instance),'mesgs');
			else setEventMessage($langs->transnoentitiesnoconv("DeleteFails"),'warnings');
		}
	}
	else setEventMessage($langs->transnoentitiesnoconv("FailedToConnectToSftp"),'errors');
}



if ($action == 'addinstalllock')
{
	// SFTP connect
	if (! function_exists("ssh2_connect")) {
		dol_print_error('','ssh2_connect function does not exists'); exit;
	}

	$server=$object->instance.'.on.dolicloud.com';
	$connection = ssh2_connect($server, 22);
	if ($connection)
	{
		//print $object->instance." ".$object->username_web." ".$object->password_web."<br>\n";
		if (! @ssh2_auth_password($connection, $object->username_web, $object->password_web))
		{
			dol_syslog("Could not authenticate with username ".$username." . and password ".$password,LOG_ERR);
		}
		else
		{
			$sftp = ssh2_sftp($connection);

			// Check if install.lock exists
			$dir=preg_replace('/_([a-zA-Z0-9]+)$/','',$object->database_db);
			$fileinstalllock="ssh2.sftp://".$sftp.$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.$dir.'/documents/install.lock';
			$fstat=stat($fileinstalllock);
			if (empty($fstat['atime']))
			{
				$stream = fopen($fileinstalllock, 'w');
				//var_dump($stream);exit;
				fwrite($stream,"// File to protect from install/upgrade.\n");
				fclose($stream);
				$fstat=stat($fileinstalllock);
				setEventMessage($langs->transnoentitiesnoconv("FileCreated"),'mesgs');
			}
			else setEventMessage($langs->transnoentitiesnoconv("ErrorFileAlreadyExists"),'warnings');

			$object->filelock=(empty($fstat['atime'])?'':$fstat['atime']);

			if (! empty($fstat['atime'])) $result = $object->update($user);
		}
	}
	else setEventMessage($langs->transnoentitiesnoconv("FailedToConnectToSftp"),'errors');
}


if ($action == 'delinstalllock')
{
	// SFTP connect
	if (! function_exists("ssh2_connect")) {
		dol_print_error('','ssh2_connect function does not exists'); exit;
	}

	$server=$object->instance.'.on.dolicloud.com';
	$connection = ssh2_connect($server, 22);
	if ($connection)
	{
		//print $object->instance." ".$object->username_web." ".$object->password_web."<br>\n";
		if (! @ssh2_auth_password($connection, $object->username_web, $object->password_web))
		{
			dol_syslog("Could not authenticate with username ".$username." . and password ".$password,LOG_ERR);
		}
		else
		{
			$sftp = ssh2_sftp($connection);

			// Check if install.lock exists
			$dir=preg_replace('/_([a-zA-Z0-9]+)$/','',$object->database_db);
			$filetodelete=$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.$dir.'/documents/install.lock';
			$result=ssh2_sftp_unlink($sftp, $filetodelete);

			if ($result) setEventMessage($langs->transnoentitiesnoconv("FileDeleted"),'mesgs');
			else setEventMessage($langs->transnoentitiesnoconv("DeleteFails"),'warnings');

			$object->filelock='';

			if ($result) $result = $object->update($user);
		}
	}
	else setEventMessage($langs->transnoentitiesnoconv("FailedToConnectToSftp"),'errors');
}


if ($action == 'refresh' || $action == 'setdate')
{
	include('lib/refresh.lib.php');

	$object->oldcopy=dol_clone($object);

	// Setup files refresh (does not update lastcheck field)
	$ret=dolicloud_files_refresh($conf,$db,$object,$errors);

	// Database refresh (also update lastcheck field)
	$ret=dolicloud_database_refresh($conf,$db,$object,$errors);

	$action = 'view';
}

?>