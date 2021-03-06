<?php
/**
 * XML Generation class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_XML
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.XML.inc.php 699 2012-09-15 11:57:13Z namiltd $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * class for generation of the xml
 *
 * @category  PHP
 * @package   PSI_XML
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class XML
{
	/**
	 * Sysinfo object where the information retrieval methods are included
	 *
	 * @var PSI_Interface_OS
	 */
	private $_sysinfo;

	/**
	 * @var System
	 */
	private $_sys = null;

	/**
	 * xml object with the xml content
	 *
	 * @var SimpleXMLExtended
	 */
	private $_xml;

	/**
	 * object for error handling
	 *
	 * @var Error
	 */
	private $_errors;

	/**
	 * array with all enabled plugins (name)
	 *
	 * @var array
	 */
	private $_plugins;

	/**
	 * plugin name if pluginrequest
	 *
	 * @var string
	 */
	private $_plugin = '';

	/**
	 * generate a xml for a plugin or for the main app
	 *
	 * @var boolean
	 */
	private $_plugin_request = false;

	/**
	 * generate the entire xml with all plugins or only a part of the xml (main or plugin)
	 *
	 * @var boolean
	 */
	private $_complete_request = false;

	/**
	 * doing some initial tasks
	 * - generate the xml structure with the right header elements
	 * - get the error object for error output
	 * - get a instance of the sysinfo object
	 *
	 * @param boolean $complete   generate xml with all plugins or not
	 * @param string  $pluginname name of the plugin
	 *
	 * @return void
	 */
	public function __construct($complete = false, $pluginname = "")
	{
		$this->_errors = PSI_Error::singleton();
		if ($pluginname == "") {
			$this->_plugin_request = false;
			$this->_plugin = '';
		} else {
			$this->_plugin_request = true;
			$this->_plugin = $pluginname;
		}
		if ($complete) {
			$this->_complete_request = true;
		} else {
			$this->_complete_request = false;
		}
		$os = PSI_OS;
		$this->_sysinfo = new $os();
		$this->_plugins = CommonFunctions::getPlugins();
		$this->_xmlbody();
	}

	/**
	 * generate common information
	 *
	 * @return void
	 */
	private function _buildVitals()
	{
		$vitals = $this->_xml->addChild('Vitals');
		$vitals->addAttribute('Hostname', $this->_sys->getHostname());
		$vitals->addAttribute('IPAddr', $this->_sys->getIp());
		$vitals->addAttribute('Kernel', $this->_sys->getKernel());
		$vitals->addAttribute('Distro', $this->_sys->getDistribution());
		$vitals->addAttribute('Distroicon', $this->_sys->getDistributionIcon());
		$vitals->addAttribute('Uptime', $this->_sys->getUptime());
		$vitals->addAttribute('Users', $this->_sys->getUsers());
		$vitals->addAttribute('LoadAvg', $this->_sys->getLoad());
		if ($this->_sys->getLoadPercent() !== null) {
			$vitals->addAttribute('CPULoad', $this->_sys->getLoadPercent());
		}
		if ($this->_sysinfo->getLanguage() !== null) {
			$vitals->addAttribute('SysLang', $this->_sysinfo->getLanguage());
		}
		if ($this->_sysinfo->getEncoding() !== null) {
			$vitals->addAttribute('CodePage', $this->_sysinfo->getEncoding());
		}

		//processes
		if (($procss = $this->_sys->getProcesses()) !== null) {
			if (isset($procss['*']) && (($procall = $procss['*']) > 0)) {
				$vitals->addAttribute('Processes', $procall);
				if (!isset($procss[' ']) || !($procss[' '] > 0)) { // not unknown
					$procsum = 0;
					if (isset($procss['R']) && (($proctmp = $procss['R']) > 0)) {
						$vitals->addAttribute('ProcessesRunning', $proctmp);
						$procsum += $proctmp;
					}
					if (isset($procss['S']) && (($proctmp = $procss['S']) > 0)) {
						$vitals->addAttribute('ProcessesSleeping', $proctmp);
						$procsum += $proctmp;
					}
					if (isset($procss['T']) && (($proctmp = $procss['T']) > 0)) {
						$vitals->addAttribute('ProcessesStopped', $proctmp);
						$procsum += $proctmp;
					}
					if (isset($procss['Z']) && (($proctmp = $procss['Z']) > 0)) {
						$vitals->addAttribute('ProcessesZombie', $proctmp);
						$procsum += $proctmp;
					}
					if (isset($procss['D']) && (($proctmp = $procss['D']) > 0)) {
						$vitals->addAttribute('ProcessesWaiting', $proctmp);
						$procsum += $proctmp;
					}
					if (($proctmp = $procall - $procsum) > 0) {
						$vitals->addAttribute('ProcessesOther', $proctmp);
					}
				}
			}
		}
		$vitals->addAttribute('OS', PSI_OS);
	}

	/**
	 * generate the network information
	 *
	 * @return void
	 */
	private function _buildNetwork()
	{
		$hideDevices = array();
		$network = $this->_xml->addChild('Network');
		if (defined('PSI_HIDE_NETWORK_INTERFACE')) {
			if (is_string(PSI_HIDE_NETWORK_INTERFACE)) {
				if (preg_match(ARRAY_EXP, PSI_HIDE_NETWORK_INTERFACE)) {
					$hideDevices = eval(PSI_HIDE_NETWORK_INTERFACE);
				} else {
					$hideDevices = array(PSI_HIDE_NETWORK_INTERFACE);
				}
			} elseif (PSI_HIDE_NETWORK_INTERFACE === true) {
				return;
			}
		}
		foreach ($this->_sys->getNetDevices() as $dev) {
			if (!in_array(trim($dev->getName()), $hideDevices)) {
				$device = $network->addChild('NetDevice');
				$device->addAttribute('Name', $dev->getName());
				$device->addAttribute('RxBytes', $dev->getRxBytes());
				$device->addAttribute('TxBytes', $dev->getTxBytes());
				$device->addAttribute('Err', $dev->getErrors());
				$device->addAttribute('Drops', $dev->getDrops());
				if (defined('PSI_SHOW_NETWORK_INFOS') && PSI_SHOW_NETWORK_INFOS && $dev->getInfo())
					$device->addAttribute('Info', $dev->getInfo());
			}
		}
	}

	/**
	 * generate the hardware information
	 *
	 * @return void
	 */
	private function _buildHardware()
	{
		$dev = new HWDevice();
		$hardware = $this->_xml->addChild('Hardware');
		if ($this->_sys->getMachine() != "") {
			$hardware->addAttribute('Name', $this->_sys->getMachine());
		}
		$pci = null;
		foreach (System::removeDupsAndCount($this->_sys->getPciDevices()) as $dev) {
			if ($pci === null) $pci = $hardware->addChild('PCI');
			$tmp = $pci->addChild('Device');
			$tmp->addAttribute('Name', $dev->getName());
			$tmp->addAttribute('Count', $dev->getCount());
		}
		$usb = null;
		foreach (System::removeDupsAndCount($this->_sys->getUsbDevices()) as $dev) {
			if ($usb === null) $usb = $hardware->addChild('USB');
			$tmp = $usb->addChild('Device');
			$tmp->addAttribute('Name', $dev->getName());
			$tmp->addAttribute('Count', $dev->getCount());
		}
		$ide = null;
		foreach (System::removeDupsAndCount($this->_sys->getIdeDevices()) as $dev) {
			if ($ide === null) $ide = $hardware->addChild('IDE');
			$tmp = $ide->addChild('Device');
			$tmp->addAttribute('Name', $dev->getName());
			$tmp->addAttribute('Count', $dev->getCount());
			if ($dev->getCapacity() !== null) {
				$tmp->addAttribute('Capacity', $dev->getCapacity());
			}
		}
		$scsi = null;
		foreach (System::removeDupsAndCount($this->_sys->getScsiDevices()) as $dev) {
			if ($scsi === null) $scsi = $hardware->addChild('SCSI');
			$tmp = $scsi->addChild('Device');
			$tmp->addAttribute('Name', $dev->getName());
			$tmp->addAttribute('Count', $dev->getCount());
			if ($dev->getCapacity() !== null) {
				$tmp->addAttribute('Capacity', $dev->getCapacity());
			}
		}
		$tb = null;
		foreach (System::removeDupsAndCount($this->_sys->getTbDevices()) as $dev) {
			if ($tb === null) $tb = $hardware->addChild('TB');
			$tmp = $tb->addChild('Device');
			$tmp->addAttribute('Name', $dev->getName());
			$tmp->addAttribute('Count', $dev->getCount());
		}
		$i2c = null;
		foreach (System::removeDupsAndCount($this->_sys->getI2cDevices()) as $dev) {
			if ($i2c === null) $i2c = $hardware->addChild('I2C');
			$tmp = $i2c->addChild('Device');
			$tmp->addAttribute('Name', $dev->getName());
			$tmp->addAttribute('Count', $dev->getCount());
		}

		$cpu = null;
		foreach ($this->_sys->getCpus() as $oneCpu) {
			if ($cpu === null) $cpu = $hardware->addChild('CPU');
			$tmp = $cpu->addChild('CpuCore');
			$tmp->addAttribute('Model', $oneCpu->getModel());
			if ($oneCpu->getCpuSpeed() !== 0) {
				$tmp->addAttribute('CpuSpeed', $oneCpu->getCpuSpeed());
			}
			if ($oneCpu->getCpuSpeedMax() !== 0) {
				$tmp->addAttribute('CpuSpeedMax', $oneCpu->getCpuSpeedMax());
			}
			if ($oneCpu->getCpuSpeedMin() !== 0) {
				$tmp->addAttribute('CpuSpeedMin', $oneCpu->getCpuSpeedMin());
			}
			if ($oneCpu->getTemp() !== null) {
				$tmp->addAttribute('CpuTemp', $oneCpu->getTemp());
			}
			if ($oneCpu->getBusSpeed() !== null) {
				$tmp->addAttribute('BusSpeed', $oneCpu->getBusSpeed());
			}
			if ($oneCpu->getCache() !== null) {
				$tmp->addAttribute('Cache', $oneCpu->getCache());
			}
			if ($oneCpu->getVirt() !== null) {
				$tmp->addAttribute('Virt', $oneCpu->getVirt());
			}
			if ($oneCpu->getBogomips() !== null) {
				$tmp->addAttribute('Bogomips', $oneCpu->getBogomips());
			}
			if ($oneCpu->getLoad() !== null) {
				$tmp->addAttribute('Load', $oneCpu->getLoad());
			}
		}
	}

	/**
	 * generate the memory information
	 *
	 * @return void
	 */
	private function _buildMemory()
	{
		$memory = $this->_xml->addChild('Memory');
		$memory->addAttribute('Free', $this->_sys->getMemFree());
		$memory->addAttribute('Used', $this->_sys->getMemUsed());
		$memory->addAttribute('Total', $this->_sys->getMemTotal());
		$memory->addAttribute('Percent', $this->_sys->getMemPercentUsed());
		if (($this->_sys->getMemApplication() !== null) || ($this->_sys->getMemBuffer() !== null) || ($this->_sys->getMemCache() !== null)) {
			$details = $memory->addChild('Details');
			if ($this->_sys->getMemApplication() !== null) {
				$details->addAttribute('App', $this->_sys->getMemApplication());
				$details->addAttribute('AppPercent', $this->_sys->getMemPercentApplication());
			}
			if ($this->_sys->getMemBuffer() !== null) {
				$details->addAttribute('Buffers', $this->_sys->getMemBuffer());
				$details->addAttribute('BuffersPercent', $this->_sys->getMemPercentBuffer());
			}
			if ($this->_sys->getMemCache() !== null) {
				$details->addAttribute('Cached', $this->_sys->getMemCache());
				$details->addAttribute('CachedPercent', $this->_sys->getMemPercentCache());
			}
		}
		if (count($this->_sys->getSwapDevices()) > 0) {
			$swap = $memory->addChild('Swap');
			$swap->addAttribute('Free', $this->_sys->getSwapFree());
			$swap->addAttribute('Used', $this->_sys->getSwapUsed());
			$swap->addAttribute('Total', $this->_sys->getSwapTotal());
			$swap->addAttribute('Percent', $this->_sys->getSwapPercentUsed());
			$i = 1;
			foreach ($this->_sys->getSwapDevices() as $dev) {
				$swapMount = $swap->addChild('Mount');
				$this->_fillDevice($swapMount, $dev, $i++);
			}
		}
	}

	/**
	 * fill a xml element with atrributes from a disk device
	 *
	 * @param SimpleXmlExtended $mount Xml-Element
	 * @param DiskDevice        $dev   DiskDevice
	 * @param Integer           $i     counter
	 *
	 * @return Void
	 */
	private function _fillDevice(SimpleXMLExtended $mount, DiskDevice $dev, $i)
	{
		$mount->addAttribute('MountPointID', $i);
		if ($dev->getFsType()!=="") $mount->addAttribute('FSType', $dev->getFsType());
		$mount->addAttribute('Name', $dev->getName());
		$mount->addAttribute('Free', sprintf("%.0f", $dev->getFree()));
		$mount->addAttribute('Used', sprintf("%.0f", $dev->getUsed()));
		$mount->addAttribute('Total', sprintf("%.0f", $dev->getTotal()));
		$mount->addAttribute('Percent', $dev->getPercentUsed());
		if (PSI_SHOW_MOUNT_OPTION === true) {
			if ($dev->getOptions() !== null) {
				$mount->addAttribute('MountOptions', preg_replace("/,/", ", ", $dev->getOptions()));
			}
		}
		if ($dev->getPercentInodesUsed() !== null) {
			$mount->addAttribute('Inodes', $dev->getPercentInodesUsed());
		}
		if (PSI_SHOW_MOUNT_POINT === true) {
			$mount->addAttribute('MountPoint', $dev->getMountPoint());
		}
	}

	/**
	 * generate the filesysteminformation
	 *
	 * @return void
	 */
	private function _buildFilesystems()
	{
		$hideMounts = $hideFstypes = $hideDisks = array();
		$i = 1;
		if (defined('PSI_HIDE_MOUNTS') && is_string(PSI_HIDE_MOUNTS)) {
			if (preg_match(ARRAY_EXP, PSI_HIDE_MOUNTS)) {
				$hideMounts = eval(PSI_HIDE_MOUNTS);
			} else {
				$hideMounts = array(PSI_HIDE_MOUNTS);
			}
		}
		if (defined('PSI_HIDE_FS_TYPES') && is_string(PSI_HIDE_FS_TYPES)) {
			if (preg_match(ARRAY_EXP, PSI_HIDE_FS_TYPES)) {
				$hideFstypes = eval(PSI_HIDE_FS_TYPES);
			} else {
				$hideFstypes = array(PSI_HIDE_FS_TYPES);
			}
		}
		if (defined('PSI_HIDE_DISKS')) {
			if (is_string(PSI_HIDE_DISKS)) {
				if (preg_match(ARRAY_EXP, PSI_HIDE_DISKS)) {
					$hideDisks = eval(PSI_HIDE_DISKS);
				} else {
					$hideDisks = array(PSI_HIDE_DISKS);
				}
			} elseif (PSI_HIDE_DISKS === true) {
				return;
			}
		}
		$fs = $this->_xml->addChild('FileSystem');
		foreach ($this->_sys->getDiskDevices() as $disk) {
			if (!in_array($disk->getMountPoint(), $hideMounts, true) && !in_array($disk->getFsType(), $hideFstypes, true) && !in_array($disk->getName(), $hideDisks, true)) {
				$mount = $fs->addChild('Mount');
				$this->_fillDevice($mount, $disk, $i++);
			}
		}
	}

	/**
	 * generate the motherboard information
	 *
	 * @return void
	 */
	private function _buildMbinfo()
	{
		$mbinfo = $this->_xml->addChild('MBInfo');
		$temp = $fan = $volt = $power = $current = null;

		if (sizeof(unserialize(PSI_MBINFO))>0) {
			foreach (unserialize(PSI_MBINFO) as $mbinfoclass) {
				$mbinfo_data = new $mbinfoclass();
				$mbinfo_detail = $mbinfo_data->getMBInfo();

				foreach ($mbinfo_detail->getMbTemp() as $dev) {
					if ($temp == null) {
						$temp = $mbinfo->addChild('Temperature');
					}
					$item = $temp->addChild('Item');
					$item->addAttribute('Label', $dev->getName());
					$item->addAttribute('Value', $dev->getValue());
					if ($dev->getMax() !== null) {
						$item->addAttribute('Max', $dev->getMax());
					}
					if (defined('PSI_SENSOR_EVENTS') && PSI_SENSOR_EVENTS && $dev->getEvent() !== "") {
						$item->addAttribute('Event', $dev->getEvent());
					}
				}

				foreach ($mbinfo_detail->getMbFan() as $dev) {
					if ($fan == null) {
						$fan = $mbinfo->addChild('Fans');
					}
					$item = $fan->addChild('Item');
					$item->addAttribute('Label', $dev->getName());
					$item->addAttribute('Value', $dev->getValue());
					if ($dev->getMin() !== null) {
						$item->addAttribute('Min', $dev->getMin());
					}
					if (defined('PSI_SENSOR_EVENTS') && PSI_SENSOR_EVENTS && $dev->getEvent() !== "") {
						$item->addAttribute('Event', $dev->getEvent());
					}
				}

				foreach ($mbinfo_detail->getMbVolt() as $dev) {
					if ($volt == null) {
						$volt = $mbinfo->addChild('Voltage');
					}
					$item = $volt->addChild('Item');
					$item->addAttribute('Label', $dev->getName());
					$item->addAttribute('Value', $dev->getValue());
					if ($dev->getMin() !== null) {
						$item->addAttribute('Min', $dev->getMin());
					}
					if ($dev->getMax() !== null) {
						$item->addAttribute('Max', $dev->getMax());
					}
					if (defined('PSI_SENSOR_EVENTS') && PSI_SENSOR_EVENTS && $dev->getEvent() !== "") {
						$item->addAttribute('Event', $dev->getEvent());
					}
				}

				foreach ($mbinfo_detail->getMbPower() as $dev) {
					if ($power == null) {
						$power = $mbinfo->addChild('Power');
					}
					$item = $power->addChild('Item');
					$item->addAttribute('Label', $dev->getName());
					$item->addAttribute('Value', $dev->getValue());
					if ($dev->getMax() !== null) {
						$item->addAttribute('Max', $dev->getMax());
					}
					if (defined('PSI_SENSOR_EVENTS') && PSI_SENSOR_EVENTS && $dev->getEvent() !== "") {
						$item->addAttribute('Event', $dev->getEvent());
					}
				}

				foreach ($mbinfo_detail->getMbCurrent() as $dev) {
					if ($current == null) {
						$current = $mbinfo->addChild('Current');
					}
					$item = $current->addChild('Item');
					$item->addAttribute('Label', $dev->getName());
					$item->addAttribute('Value', $dev->getValue());
					if ($dev->getMax() !== null) {
						$item->addAttribute('Max', $dev->getMax());
					}
					if (defined('PSI_SENSOR_EVENTS') && PSI_SENSOR_EVENTS && $dev->getEvent() !== "") {
						$item->addAttribute('Event', $dev->getEvent());
					}
				}
			}
		}
	}

	/**
	 * generate the ups information
	 *
	 * @return void
	 */
	private function _buildUpsinfo()
	{
		$upsinfo = $this->_xml->addChild('UPSInfo');
		if (defined('PSI_UPS_APCUPSD_CGI_ENABLE') && PSI_UPS_APCUPSD_CGI_ENABLE) {
			$upsinfo->addAttribute('ApcupsdCgiLinks', true);
		}
		if (sizeof(unserialize(PSI_UPSINFO))>0) {
			foreach (unserialize(PSI_UPSINFO) as $upsinfoclass) {
				$upsinfo_data = new $upsinfoclass();
				$upsinfo_detail = $upsinfo_data->getUPSInfo();
				foreach ($upsinfo_detail->getUpsDevices() as $ups) {
					$item = $upsinfo->addChild('UPS');
					$item->addAttribute('Name', $ups->getName());
					if ($ups->getModel() !== "") {
						$item->addAttribute('Model', $ups->getModel());
					}
					$item->addAttribute('Mode', $ups->getMode());
					if ($ups->getStartTime() !== "") {
						$item->addAttribute('StartTime', $ups->getStartTime());
					}
					$item->addAttribute('Status', $ups->getStatus());
					if ($ups->getTemperatur() !== null) {
						$item->addAttribute('Temperature', $ups->getTemperatur());
					}
					if ($ups->getOutages() !== null) {
						$item->addAttribute('OutagesCount', $ups->getOutages());
					}
					if ($ups->getLastOutage() !== null) {
						$item->addAttribute('LastOutage', $ups->getLastOutage());
					}
					if ($ups->getLastOutageFinish() !== null) {
						$item->addAttribute('LastOutageFinish', $ups->getLastOutageFinish());
					}
					if ($ups->getLineVoltage() !== null) {
						$item->addAttribute('LineVoltage', $ups->getLineVoltage());
					}
					if ($ups->getLineFrequency() !== null) {
						$item->addAttribute('LineFrequency', $ups->getLineFrequency());
					}
					if ($ups->getLoad() !== null) {
						$item->addAttribute('LoadPercent', $ups->getLoad());
					}
					if ($ups->getBatteryDate() !== null) {
						$item->addAttribute('BatteryDate', $ups->getBatteryDate());
					}
					if ($ups->getBatteryVoltage() !== null) {
						$item->addAttribute('BatteryVoltage', $ups->getBatteryVoltage());
					}
					if ($ups->getBatterCharge() !== null) {
						$item->addAttribute('BatteryChargePercent', $ups->getBatterCharge());
					}
					if ($ups->getTimeLeft() !== null) {
						$item->addAttribute('TimeLeftMinutes', $ups->getTimeLeft());
					}
				}
			}
		}
	}

	/**
	 * generate the xml document
	 *
	 * @return void
	 */
	private function _buildXml()
	{
		if (!$this->_plugin_request || $this->_complete_request) {
			if (version_compare("5.2", PHP_VERSION, ">")) {
				$this->_errors->addError("ERROR", "PHP 5.2 or greater is required, some things may not work correctly");
			}
			if ($this->_sys === null) {
				if (PSI_DEBUG === true) {
					// unstable version check
					if (!is_numeric(substr(PSI_VERSION, -1))) {
						$this->_errors->addError("WARN", "This is an unstable version of phpSysInfo, some things may not work correctly");
					}

					// Safe mode check
					$safe_mode = @ini_get("safe_mode") ? true : false;
					if ($safe_mode) {
						$this->_errors->addError("WARN", "PhpSysInfo requires to set off 'safe_mode' in 'php.ini'");
					}
					// Include path check
					$include_path = @ini_get("include_path");
					if ($include_path && ($include_path!="")) {
						$include_path = preg_replace("/(:)|(;)/", "\n", $include_path);
						if (preg_match("/^\.$/m", $include_path)) {
							$include_path = ".";
						}
					}
					if ($include_path != ".") {
						$this->_errors->addError("WARN", "PhpSysInfo requires '.' inside the 'include_path' in php.ini");
					}
					// popen mode check
					if (defined("PSI_MODE_POPEN") && PSI_MODE_POPEN === true) {
						$this->_errors->addError("WARN", "Installed version of PHP does not support proc_open() function, popen() is used");
					}
				}
				$this->_sys = $this->_sysinfo->getSys();
			}
			$this->_buildVitals();
			$this->_buildNetwork();
			$this->_buildHardware();
			$this->_buildMemory();
			$this->_buildFilesystems();
			$this->_buildMbinfo();
			$this->_buildUpsinfo();
		}
		$this->_buildPlugins();
		$this->_xml->combinexml($this->_errors->errorsAddToXML($this->_sysinfo->getEncoding()));
	}

	/**
	 * get the xml object
	 *
	 * @return string
	 */
	public function getXml()
	{
		$this->_buildXml();

		return $this->_xml->getSimpleXmlElement();
	}

	/**
	 * include xml-trees of the plugins to the main xml
	 *
	 * @return void
	 */
	private function _buildPlugins()
	{
		$pluginroot = $this->_xml->addChild("Plugins");
		if (($this->_plugin_request || $this->_complete_request) && count($this->_plugins) > 0) {
			$plugins = array();
			if ($this->_complete_request) {
				$plugins = $this->_plugins;
			}
			if ($this->_plugin_request) {
				$plugins = array($this->_plugin);
			}
			foreach ($plugins as $plugin) {
				$object = new $plugin($this->_sysinfo->getEncoding());
				$object->execute();
				$oxml = $object->xml();
				if (sizeof($oxml) > 0) {
					$pluginroot->combinexml($oxml);
				}
			}
		}
	}

	/**
	 * build the xml structure where the content can be inserted
	 *
	 * @return void
	 */
	private function _xmlbody()
	{
		$dom = new DOMDocument('1.0', 'UTF-8');
		$root = $dom->createElement("tns:phpsysinfo");
		$root->setAttribute('xmlns:tns', 'http://phpsysinfo.sourceforge.net/');
		$root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$root->setAttribute('xsi:schemaLocation', 'http://phpsysinfo.sourceforge.net/ phpsysinfo3.xsd');
		$dom->appendChild($root);
		$this->_xml = new SimpleXMLExtended(simplexml_import_dom($dom), $this->_sysinfo->getEncoding());

		$generation = $this->_xml->addChild('Generation');
		$generation->addAttribute('version', PSI_VERSION_STRING);
		$generation->addAttribute('timestamp', time());
		$options = $this->_xml->addChild('Options');
		$options->addAttribute('tempFormat', defined('PSI_TEMP_FORMAT') ? strtolower(PSI_TEMP_FORMAT) : 'c');
		$options->addAttribute('byteFormat', defined('PSI_BYTE_FORMAT') ? strtolower(PSI_BYTE_FORMAT) : 'auto_binary');
		if (defined('PSI_REFRESH')) {
			if (PSI_REFRESH === false) {
				$options->addAttribute('refresh', 0);
			} elseif (PSI_REFRESH === true) {
				$options->addAttribute('refresh', 1);
			} else {
				$options->addAttribute('refresh', PSI_REFRESH);
			}
		} else {
			$options->addAttribute('refresh', 60000);
		}
		if (defined('PSI_FS_USAGE_THRESHOLD')) {
			if (PSI_FS_USAGE_THRESHOLD === true) {
				$options->addAttribute('threshold', 1);
			} elseif ((PSI_FS_USAGE_THRESHOLD !== false) && (PSI_FS_USAGE_THRESHOLD >= 1) && (PSI_FS_USAGE_THRESHOLD <= 99)) {
				$options->addAttribute('threshold', PSI_FS_USAGE_THRESHOLD);
			}
		} else {
			$options->addAttribute('threshold', 90);
		}
		if (count($this->_plugins) > 0) {
			if ($this->_plugin_request) {
				$plug = $this->_xml->addChild('UsedPlugins');
				$plug->addChild('Plugin')->addAttribute('name', $this->_plugin);
			} elseif ($this->_complete_request) {
				$plug = $this->_xml->addChild('UsedPlugins');
				foreach ($this->_plugins as $plugin) {
					$plug->addChild('Plugin')->addAttribute('name', $plugin);
				}
				/*
				} else {
				$plug = $this->_xml->addChild('UnusedPlugins');
				foreach ($this->_plugins as $plugin) {
					$plug->addChild('Plugin')->addAttribute('name', $plugin);
				}
				*/
			}
		}
	}
}
