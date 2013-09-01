<?php
// Files with some lib

include_once(DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php');


/**
 * Process refresh of setup files for customer $object.
 * This does not update any lastcheck fields.
 *
 * @param 	Conf				$conf		Conf
 * @param 	Database			$db			Database handler
 * @param 	DoliCloudCustomer 	&$object	Customer (can modify caller)
 * @param	array				&$errors	Array of errors
 * @return	int								1
 */
function dolicloud_files_refresh($conf, $db, &$object, &$errors)
{
	// SFTP refresh
	if (function_exists("ssh2_connect"))
	{
		$server=$object->instance.'.on.dolicloud.com';
		$connection = ssh2_connect($server, 22);
		if ($connection)
		{
			//print $object->instance." ".$object->username_web." ".$object->password_web."<br>\n";
			if (! @ssh2_auth_password($connection, $object->username_web, $object->password_web))
			{
				dol_syslog("Could not authenticate with username ".$object->username_web." . and password ".$object->password_web,LOG_ERR);
			}
			else
			{
				$sftp = ssh2_sftp($connection);

				$dir=preg_replace('/_dolibarr$/','',$object->database_db);
				$file="ssh2.sftp://".$sftp.$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.$dir.'/htdocs/conf/conf.php';

				//print $file;
				$stream = fopen($file, 'r');
				$fstat=fstat($stream);
				fclose($stream);
				//var_dump($fstat);

				// Update ssl certificate
				// Dir .ssh must have rwx------ permissions
				// File authorized_keys must have rw------- permissions

				// Check if authorized_key exists
				$filecert="ssh2.sftp://".$sftp.$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/.ssh/authorized_keys';
				$fstat=stat($filecert);
				// Create authorized_keys file
				if (empty($fstat['atime']))
				{
					$stream = fopen($filecert, 'w');
					//var_dump($stream);exit;
					fwrite($stream,"ssh-dss AAAAB3NzaC1kc3MAAACBAKu0WcYS8t02uoInHqyxKxQ7qOJaoOw1bRPPSzEKeXZcdHcBffEHpgLUTYEuk8x6rviQ0yRp960NyrjZNCe1rn5cXWuZpJQe/dBGuVMdSK0LiCr6xar66XOsuDDssZn3w0u97pId8wMrsYBzFUj/J3XSbAf5gX5MfWiUuPG+ZcyPAAAAFQCnXg8nISCy6fs11Lo0UXH4fUuSCwAAAIB5TqwLW4lrA0GavA/HG4sS3BdRE8ZxgKRkqY/LQGmVT7MOTCpae97YT7vA8AkPFOpVZWX9qpYD1EjvJlcB9PASmROSV1JCwxXsEK0vxc+MsogqNJTYifdonEjQJJ8dLKh0KPkXoBrTJnn7xNzdarukbiYPDNvH2/OaXUdkrrUoFwAAAIACief5fwRcSeS3R3uTIyoVUBJGhjtOxkEnS6kMvXpdrLi6nMGQvAxsusVhT60gZNHZpOd8zbs0RWI6hBttZl+zd2yK16PFzLbZYR//sQW0vrV4662KbkcgclYNATbVzrZjPUi6LeJ+1PA/n0pI4leWhD+w7hWEPWEkGVGBrwKFAA== admin@apollon1.nltechno.com\nssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAIEAp6Nj1j5jVgziTIRPiWIdqm95P+yT5wAFYzzyzy5g1/ip+YRz6DT+TJUnpI3+coKPtTGahFkHRUIxCMBBObbgkpw0wJr9aBJrZ4YNSIe+DdmIe0JU4L40eHtOcxDNRFCeS8n9LaQ3/K+UV6JEhplibLYEhPKPn4fTfm7Krj0KDVc= admin@apollon1.nltechno.com\n");
					fclose($stream);
					$fstat=stat($filecert);
				}
				$object->fileauthorizedkey=(empty($fstat['mtime'])?'':$fstat['mtime']);

				// Check if install.lock exists
				$fileinstalllock="ssh2.sftp://".$sftp.$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.$dir.'/documents/install.lock';
				$fstatlock=stat($fileinstalllock);
				$object->filelock=(empty($fstatlock['atime'])?'':$fstatlock['atime']);

				// Define dates
				if (empty($object->date_registration) || empty($object->date_endfreeperiod))
				{
					// Overwrite only if not defined
					$object->date_registration=$fstatlock['mtime'];
					//$object->date_endfreeperiod=dol_time_plus_duree($object->date_registration,1,'m');
					$object->date_endfreeperiod=($object->date_registration?dol_time_plus_duree($object->date_registration,15,'d'):'');
				}
			}
		}
		else {
			$errors[]='Failed to connect to ssh2 to '.$server;
		}
	}
	else {
		$errors[]='ssh2_connect not supported by this PHP';
	}

	return 1;
}


/**
 * Process refresh of database for customer $object
 * This also update database field lastcheck.
 *
 * @param 	Conf				$conf		Conf
 * @param 	Database			$db			Database handler
 * @param 	DoliCloudCustomer 	&$object	Customer (can modify caller)
 * @param	array				&$errors	Array of errors
 * @return	int								1
 */
function dolicloud_database_refresh($conf, $db, &$object, &$errors)
{
	$newdb=getDoliDBInstance($conf->db->type, $object->instance.'.on.dolicloud.com', $object->username_db, $object->password_db, $object->database_db, 3306);
	if (is_object($newdb))
	{
		$error=0;
		$done=0;

		if ($newdb->connected)
		{
			// Get user/pass of last admin user
			if (! $error)
			{
				$sql="SELECT login, pass FROM llx_user WHERE admin = 1 ORDER BY statut DESC, datelastlogin DESC LIMIT 1";
				dol_syslog('sql='.$sql);
				$resql=$newdb->query($sql);
				if ($resql)
				{
					$obj = $newdb->fetch_object($resql);
					$object->lastlogin_admin=$obj->login;
					$object->lastpass_admin=$obj->pass;
					$lastloginadmin=$object->lastlogin_admin;
					$lastpassadmin=$object->lastpass_admin;
				}
				else $error++;
			}

			// Get list of modules
			if (! $error)
			{
				$modulesenabled=array(); $lastinstall=''; $lastupgrade='';
				$sql="SELECT name, value FROM llx_const WHERE name LIKE 'MAIN_MODULE_%' or name = 'MAIN_VERSION_LAST_UPGRADE' or name = 'MAIN_VERSION_LAST_INSTALL'";
				dol_syslog('sql='.$sql);
				$resql=$newdb->query($sql);
				if ($resql)
				{
					$num=$newdb->num_rows($resql);
					$i=0;
					while ($i < $num)
					{
						$obj = $newdb->fetch_object($resql);
						if (preg_match('/MAIN_MODULE_/',$obj->name))
						{
							$name=preg_replace('/^[^_]+_[^_]+_/','',$obj->name);
							if (! preg_match('/_/',$name)) $modulesenabled[$name]=$name;
						}
						if (preg_match('/MAIN_VERSION_LAST_UPGRADE/',$obj->name))
						{
							$lastupgrade=$obj->value;
						}
						if (preg_match('/MAIN_VERSION_LAST_INSTALL/',$obj->name))
						{
							$lastinstall=$obj->value;
						}
						$i++;
					}
					$object->modulesenabled=join(',',$modulesenabled);
					$object->version=($lastupgrade?$lastupgrade:$lastinstall);
				}
				else $error++;
			}

			// Get nb of users
			if (! $error)
			{
				$sql="SELECT COUNT(login) as nbofusers FROM llx_user WHERE statut <> 0";
				dol_syslog('sql='.$sql);
				$resql=$newdb->query($sql);
				if ($resql)
				{
					$obj = $newdb->fetch_object($resql);
					$object->nbofusers	= $obj->nbofusers;
				}
				else $error++;
			}

			$deltatzserver=(getServerTimeZoneInt()-0)*3600;	// Diff between TZ of NLTechno and DoliCloud

			// Get last login of users
			if (! $error)
			{
				$sql="SELECT login, pass, datelastlogin FROM llx_user WHERE statut <> 0 ORDER BY datelastlogin DESC LIMIT 1";
				dol_syslog('sql='.$sql);
				$resql=$newdb->query($sql);
				if ($resql)
				{
					$obj = $newdb->fetch_object($resql);

					$object->lastlogin  = $obj->login;
					$object->lastpass   = $obj->pass;
					$object->date_lastlogin = ($obj->datelastlogin ? ($newdb->jdate($obj->datelastlogin)+$deltatzserver) : '');
				}
				else
				{
					$error++;
					$errors[]='Failed to connect to database '.$object->instance.'.on.dolicloud.com'.' '.$object->username_db;
				}
			}

			$done++;
		}
		else
		{
			$errors[]='Failed to connect '.$conf->db->type.' '.$object->instance.'.on.dolicloud.com '.$object->username_db.' '.$object->password_db.' '.$object->database_db.' 3306';
		}

		$newdb->close();

		if (! $error && $done)
		{
			$now=dol_now();
			$object->lastcheck=$now;

			$result = $object->update($user);	// persist

			if ($result < 0)
			{
				dol_syslog("Failed to persist data on object into database", LOG_ERR);
				if ($object->error) $errors[]=$object->error;
				$errors=array_merge($errors,$object->errors);
			}
		}
	}
	else
	{
		$errors[]='Failed to connect '.$conf->db->type.' '.$object->instance.'.on.dolicloud.com '.$object->username_db.' '.$object->password_db.' '.$object->database_db.' 3306';
	}

	return 1;
}

?>