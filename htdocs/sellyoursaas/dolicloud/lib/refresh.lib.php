<?php
// Files with some lib

// Show totals
$serverlocation=185.9;	// Price dollar
$dollareuro=0.78;		// Price euro
$serverprice=price2num($serverlocation * $dollareuro, 'MT');
$part=0.3;	// 30%


include_once(DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php');


/**
 * Process refresh of setup files for customer $object.
 * This does not update any lastcheck fields.
 *
 * @param 	Conf				$conf		Conf
 * @param 	Database			$db			Database handler
 * @param 	DoliCloudCustomer 	$object	    Customer (can modify caller)
 * @param	array				$errors	    Array of errors
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
				if (! $sftp)
				{
					dol_syslog("Could not execute ssh2_sftp",LOG_ERR);
					$errors[]='Failed to connect to ssh2 to '.$server;
					return 1;
				}

				$dir=preg_replace('/_([a-zA-Z0-9]+)$/','',$object->database_db);
				//$file="ssh2.sftp://".$sftp.$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.$dir.'/htdocs/conf/conf.php';
				$file="ssh2.sftp://".intval($sftp).$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.$dir.'/htdocs/conf/conf.php';    // With PHP 5.6.27+

				//print $file;
				$stream = fopen($file, 'r');
				$fstat=ssh2_sftp_stat($sftp, $conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.$dir.'/htdocs/conf/conf.php');
				fclose($stream);
				//var_dump($fstat);

				// Update ssl certificate
				// Dir .ssh must have rwx------ permissions
				// File authorized_keys must have rw------- permissions

				// Check if authorized_key exists
				//$filecert="ssh2.sftp://".$sftp.$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/.ssh/authorized_keys';
				$filecert="ssh2.sftp://".intval($sftp).$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/.ssh/authorized_keys';
				$fstat=ssh2_sftp_stat($sftp, $conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/.ssh/authorized_keys');
				// Create authorized_keys file
				if (empty($fstat['atime']))
				{
					$stream = fopen($filecert, 'w');
					//var_dump($stream);exit;
					fwrite($stream,"ssh-dss AAAAB3NzaC1kc3MAAACBAKu0WcYS8t02uoInHqyxKxQ7qOJaoOw1bRPPSzEKeXZcdHcBffEHpgLUTYEuk8x6rviQ0yRp960NyrjZNCe1rn5cXWuZpJQe/dBGuVMdSK0LiCr6xar66XOsuDDssZn3w0u97pId8wMrsYBzFUj/J3XSbAf5gX5MfWiUuPG+ZcyPAAAAFQCnXg8nISCy6fs11Lo0UXH4fUuSCwAAAIB5TqwLW4lrA0GavA/HG4sS3BdRE8ZxgKRkqY/LQGmVT7MOTCpae97YT7vA8AkPFOpVZWX9qpYD1EjvJlcB9PASmROSV1JCwxXsEK0vxc+MsogqNJTYifdonEjQJJ8dLKh0KPkXoBrTJnn7xNzdarukbiYPDNvH2/OaXUdkrrUoFwAAAIACief5fwRcSeS3R3uTIyoVUBJGhjtOxkEnS6kMvXpdrLi6nMGQvAxsusVhT60gZNHZpOd8zbs0RWI6hBttZl+zd2yK16PFzLbZYR//sQW0vrV4662KbkcgclYNATbVzrZjPUi6LeJ+1PA/n0pI4leWhD+w7hWEPWEkGVGBrwKFAA== admin@apollon1.nltechno.com\nssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAIEAp6Nj1j5jVgziTIRPiWIdqm95P+yT5wAFYzzyzy5g1/ip+YRz6DT+TJUnpI3+coKPtTGahFkHRUIxCMBBObbgkpw0wJr9aBJrZ4YNSIe+DdmIe0JU4L40eHtOcxDNRFCeS8n9LaQ3/K+UV6JEhplibLYEhPKPn4fTfm7Krj0KDVc= admin@apollon1.nltechno.com\n");
					fclose($stream);
					$fstat=ssh2_sftp_stat($sftp, $conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/.ssh/authorized_keys');
				}
				$object->fileauthorizedkey=(empty($fstat['mtime'])?'':$fstat['mtime']);

				// Check if install.lock exists
				//$fileinstalllock="ssh2.sftp://".$sftp.$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.$dir.'/documents/install.lock';
				$fileinstalllock="ssh2.sftp://".intval($sftp).$conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.$dir.'/documents/install.lock';
				$fstatlock=ssh2_sftp_stat($sftp, $conf->global->DOLICLOUD_EXT_HOME.'/'.$object->username_web.'/'.$dir.'/documents/install.lock');
				$object->filelock=(empty($fstatlock['atime'])?'':$fstatlock['atime']);

				// Define dates
				/*if (empty($object->date_registration) || empty($object->date_endfreeperiod))
				{
					// Overwrite only if not defined
					$object->date_registration=$fstatlock['mtime'];
					//$object->date_endfreeperiod=dol_time_plus_duree($object->date_registration,1,'m');
					$object->date_endfreeperiod=($object->date_registration?dol_time_plus_duree($object->date_registration,15,'d'):'');
				}*/
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
 * This set a lot of object->xxx properties (lastlogin_admin, lastpass_admin, nbofusers,
 * modulesenabled, version, date_lastcheck, lastcheck)
 *
 * @param 	Conf				$conf		Conf
 * @param 	Database			$db			Database handler
 * @param 	DoliCloudCustomer 	$object	    Customer (can modify caller)
 * @param	array				$errors	    Array of errors
 * @return	int								1
 */
function dolicloud_database_refresh($conf, $db, &$object, &$errors)
{
	$newdb=getDoliDBInstance('mysqli', $object->instance.'.on.dolicloud.com', $object->username_db, $object->password_db, $object->database_db, 3306);

	$ret=1;

	unset($object->lastlogin);
	unset($object->lastpass);
	unset($object->date_lastlogin);
	unset($object->date_lastcheck);
	unset($object->lastlogin_admin);
	unset($object->lastpass_admin);
	unset($object->modulesenabled);
	unset($object->version);
	unset($object->nbofusers);

	if (is_object($newdb))
	{
		$error=0;
		$done=0;

		if ($newdb->connected && $newdb->database_selected)
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
			$ret=-1;
		}

		$newdb->close();

		if (! $error && $done)
		{
			$now=dol_now();
			$object->date_lastcheck=$now;
			$object->lastcheck=$now;	// For backward compatibility

			$result = $object->update($user);	// persist
			if (method_exists($object,'update_old')) $result = $object->update_old($user);	// persist

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
		$ret=-1;
	}

	return $ret;
}


/**
 * Calculate stats ('total', 'totalcommissions', 'totalinstancespaying' (nbclients 'ACTIVE' not at trial), 'totalinstances' (nb clients not at trial, include suspended), 'totalusers')
 * at date datelim (or realtime if date is empty)
 *
 * Rem: Comptage des users par status
 * SELECT sum(im.value), c.status as customer_status, i.status as instance_status, s.payment_status
 * FROM app_instance as i LEFT JOIN app_instance_meter as im ON i.id = im.app_instance_id AND im.meter_id = 1, customer as c
 * LEFT JOIN channel_partner_customer as cc ON cc.customer_id = c.id LEFT JOIN channel_partner as cp ON cc.channel_partner_id = cp.id LEFT JOIN person as per ON c.primary_contact_id = per.id, subscription as s, plan as pl
 * LEFT JOIN plan_add_on as pao ON pl.id=pao.plan_id and pao.meter_id = 1, app_package as p
 * WHERE i.customer_id = c.id AND c.id = s.customer_id AND s.plan_id = pl.id AND pl.app_package_id = p.id AND s.payment_status NOT IN ('TRIAL', 'TRIALING', 'TRIAL_EXPIRED') AND i.deployed_date <= '20141201005959'
 * group by c.status,  i.status, s.payment_status
 * order by sum(im.value) desc
 *
 * @param	Database	$db			Database handler
 * @param	date		$datelim	Date limit
 * @return	array					Array of data
 */
function dolicloud_calculate_stats($db, $datelim)
{
	$total = $totalcommissions = $totalinstancespaying = $totalinstances = $totalusers = 0;
	$listofcustomers=array(); $listofcustomerspaying=array();

	$sql = "SELECT";
	$sql.= " i.id,";

	$sql.= " i.version,";
	$sql.= " i.app_package_id,";
	$sql.= " i.created_date as date_registration,";
	$sql.= " i.customer_id,";
	$sql.= " i.db_name,";
	$sql.= " i.db_password,";
	$sql.= " i.db_port,";
	$sql.= " i.db_server,";
	$sql.= " i.db_username,";
	$sql.= " i.default_password,";
	$sql.= " i.deployed_date,";
	$sql.= " i.domain_id,";
	$sql.= " i.fs_path,";
	$sql.= " i.install_time,";
	$sql.= " i.ip_address,";
	$sql.= " i.last_login as date_lastlogin,";
	$sql.= " i.last_updated,";
	$sql.= " i.name as instance,";
	$sql.= " i.os_password,";
	$sql.= " i.os_username,";
	$sql.= " i.rm_install_url,";
	$sql.= " i.rm_web_app_name,";
	$sql.= " i.status as instance_status,";
	$sql.= " i.undeployed_date,";
	$sql.= " i.access_enabled,";
	$sql.= " i.default_username,";
	$sql.= " i.ssh_port,";

	$sql.= " p.id as planid,";
	$sql.= " p.name as plan,";

	$sql.= " im.value as nbofusers,";
	$sql.= " im.last_updated as lastcheck,";

	$sql.= " pao.amount as price_user,";
	$sql.= " pao.min_threshold as min_threshold,";

	$sql.= " pl.amount as price_instance,";
	$sql.= " pl.meter_id as plan_meter_id,";
	$sql.= " pl.name as plan,";
	$sql.= " pl.interval_unit as interval_unit,";
	
	$sql.= " c.org_name as organization,";
	$sql.= " c.status as status,";
	$sql.= " c.past_due_start,";
	$sql.= " c.suspension_date,";

	$sql.= " s.payment_status,";
	$sql.= " s.status as subscription_status,";

	$sql.= " per.username as email,";
	$sql.= " per.first_name as firstname,";
	$sql.= " per.last_name as lastname,";

	$sql.= " cp.org_name as partner";

	$sql.= " FROM app_instance as i";
	$sql.= " LEFT JOIN app_instance_meter as im ON i.id = im.app_instance_id AND im.meter_id = 1,";	// meter_id = 1 = users
	$sql.= " customer as c";
	$sql.= " LEFT JOIN channel_partner_customer as cc ON cc.customer_id = c.id";
	$sql.= " LEFT JOIN channel_partner as cp ON cc.channel_partner_id = cp.id";
	$sql.= " LEFT JOIN person as per ON c.primary_contact_id = per.id,";
	$sql.= " subscription as s, plan as pl";
	$sql.= " LEFT JOIN plan_add_on as pao ON pl.id=pao.plan_id and pao.meter_id = 1,";	// meter_id = 1 = users
	$sql.= " app_package as p";
	$sql.= " WHERE i.customer_id = c.id AND c.id = s.customer_id AND s.plan_id = pl.id AND pl.app_package_id = p.id";
	$sql.= " AND s.payment_status NOT IN ('TRIAL', 'TRIALING', 'TRIAL_EXPIRED')";	// We keep OK, FAILURE, PAST_DUE
	if ($datelim) $sql.= " AND i.deployed_date <= '".$db->idate($datelim)."'";

	dol_syslog($script_file." dolicloud_calculate_stats sql=".$sql, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql)
	{
	    $num = $db->num_rows($resql);
	    $i = 0;
	    if ($num)
	    {
	        while ($i < $num)
	        {
	            $obj = $db->fetch_object($resql);
	            if ($obj)
	            {
    				//print "($obj->price_instance * ($obj->plan_meter_id == 1 ? $obj->nbofusers : 1)) + (max(0,($obj->nbofusers - ($obj->min_threshold ? $obj->min_threshold : 0))) * $obj->price_user)";
                    // Voir aussi dolicloud_list_new.php
                    $price=($obj->price_instance * ($obj->plan_meter_id == 1 ? $obj->nbofusers : 1)) + (max(0,($obj->nbofusers - ($obj->min_threshold ? $obj->min_threshold : 0))) * $obj->price_user);
                    if ($obj->interval_unit == 'Year') $price = $price / 12;
					
					$totalinstances++;
					$totalusers+=$obj->nbofusers;

					$activepaying=1;
					if (in_array($obj->status,array('SUSPENDED'))) $activepaying=0;
					if (in_array($obj->status,array('CLOSED','CLOSE_QUEUED','CLOSURE_REQUESTED')) || in_array($obj->instance_status,array('UNDEPLOYED'))) $activepaying=0;
					if (in_array($obj->payment_status,array('TRIAL','TRIALING','TRIAL_EXPIRED','FAILURE','PAST_DUE')) || in_array($obj->status,array('CLOSED','CLOSE_QUEUED','CLOSURE_REQUESTED')) || in_array($obj->instance_status,array('UNDEPLOYED'))) $activepaying=0;

	                if (! $activepaying)
	                {
	                	$listofcustomers[$obj->customer_id]++;
						//print "cpt=".$totalinstances." customer_id=".$obj->customer_id." instance=".$obj->instance." status=".$obj->status." instance_status=".$obj->instance_status." payment_status=".$obj->payment_status." => Price = ".$obj->price_instance.' * '.($obj->plan_meter_id == 1 ? $obj->nbofusers : 1)." + ".max(0,($obj->nbofusers - $obj->min_threshold))." * ".$obj->price_user." = ".$price." -> 0<br>\n";
	                }
	                else
	              {
	              		$listofcustomerspaying[$obj->customer_id]++;

	                	$totalinstancespaying++;
	                	$total+=$price;

	                	//print "cpt=".$totalinstancespaying." customer_id=".$obj->customer_id." instance=".$obj->instance." status=".$obj->status." instance_status=".$obj->instance_status." payment_status=".$obj->payment_status." => Price = ".$obj->price_instance.' * '.($obj->plan_meter_id == 1 ? $obj->nbofusers : 1)." + ".max(0,($obj->nbofusers - $obj->min_threshold))." * ".$obj->price_user." = ".$price."<br>\n";
	                	if (! empty($obj->partner))
	                	{
	                		$totalcommissions+=price2num($price * 0.2);
	                	}
	                }
	            }
	            $i++;
	        }
	    }
	}
	else
	{
	    $error++;
	    dol_print_error($db);
	}

	return array('total'=>(double) $total, 'totalcommissions'=>(double) $totalcommissions,
				   'totalinstancespaying'=>(int) $totalinstancespaying,'totalinstances'=>(int) $totalinstances, 'totalusers'=>(int) $totalusers,
				   'totalcustomerspaying'=>(int) count($listofcustomerspaying), 'totalcustomers'=>(int) count($listofcustomers)
		);
}
