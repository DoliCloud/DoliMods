<?php
/**
 * qtstemp sensor class
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   SVN: $Id: class.qtssnmp.inc.php 661 2012-08-27 11:26:39Z namiltd $
 * @link      http://phpsysinfo.sourceforge.net
 */
 /**
 * getting hardware temperature information through snmpwalk
 *
 * @category  PHP
 * @package   PSI_Sensor
 * @author    Michael Cramer <BigMichi1@users.sourceforge.net>
 * @author    William Johansson <radar@radhuset.org>
 * @copyright 2009 phpSysInfo
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @version   Release: 3.0
 * @link      http://phpsysinfo.sourceforge.net
 */
class QTSsnmp extends Sensors
{
	/**
	 * get temperature information
	 *
	 * @return void
	 */
	private function _temperature()
	{
		if (CommonFunctions::executeProgram("/Apps/opt/bin/snmpwalk", "-Ona -c public -v 1 -r 1 127.0.0.1 .1.3.6.1.4.1.24681.1.2.5.0", $buffer, PSI_DEBUG)
		   && preg_match('/^[\.\d]+ = STRING:\s\"?(\d+)\sC/', $buffer, $data)) {
			$dev = new SensorDevice();
			$dev->setName("CPU");
			$dev->setValue($data[1]);
			$this->mbinfo->setMbTemp($dev);
		}

		if (CommonFunctions::executeProgram("/Apps/opt/bin/snmpwalk", "-Ona -c public -v 1 -r 1 127.0.0.1 .1.3.6.1.4.1.24681.1.2.6.0", $buffer, PSI_DEBUG)
		   && preg_match('/^[\.\d]+ = STRING:\s\"?(\d+)\sC/', $buffer, $data)) {
			$dev = new SensorDevice();
			$dev->setName("System");
			$dev->setValue($data[1]);
			$this->mbinfo->setMbTemp($dev);
		}

		if (CommonFunctions::executeProgram("/Apps/opt/bin/snmpwalk", "-Ona -c public -v 1 -r 1 127.0.0.1 .1.3.6.1.4.1.24681.1.2.11.1.3", $buffer, PSI_DEBUG)) {
			$lines = preg_split('/\r?\n/', $buffer);
			foreach ($lines as $line) if (preg_match('/^[\.\d]+\.(\d+) = STRING:\s\"?(\d+)\sC/', $line, $data)) {
				$dev = new SensorDevice();
				$dev->setName("HDD ".$data[1]);
				$dev->setValue($data[2]);
				$this->mbinfo->setMbTemp($dev);
			}
		}
	}

	/**
	 * get fan information
	 *
	 * @return void
	 */
	private function _fans()
	{
		if (CommonFunctions::executeProgram("/Apps/opt/bin/snmpwalk", "-Ona -c public -v 1 -r 1 127.0.0.1 .1.3.6.1.4.1.24681.1.2.15.1.3", $buffer, PSI_DEBUG)) {
			$lines = preg_split('/\r?\n/', $buffer);
			foreach ($lines as $line) if (preg_match('/^[\.\d]+\.(\d+) = STRING:\s\"?(\d+)\sRPM/', $line, $data)) {
				$dev = new SensorDevice();
				$dev->setName("Fan ".$data[1]);
				$dev->setValue($data[2]);
				$this->mbinfo->setMbFan($dev);
			}
		}
	}

	/**
	 * get the information
	 *
	 * @see PSI_Interface_Sensor::build()
	 *
	 * @return Void
	 */
	public function build()
	{
		$this->_temperature();
		$this->_fans();
	}
}
