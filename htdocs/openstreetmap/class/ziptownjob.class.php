<?php
/* This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file			htdocs/openstreetmap/class/ziptownjob.class.php
 *  \brief			Scheduled job of the module OpenStreetMap: import of the zip codes and towns waiting in the queue
 */


/**
 *  Class called by the scheduled jobs of Dolibarr (module Cron)
 */
class OpenStreetMapZiptownJob
{
	/**
	 * @var DoliDB Database handler
	 */
	public $db;

	/**
	 * @var string Result shown in the list of scheduled jobs
	 */
	public $output = '';

	/**
	 * @var string Error
	 */
	public $error = '';

	/**
	 *  Constructor
	 *
	 *  @param	DoliDB	$db		Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 *  Import the next parts of the files waiting in the queue, for at most OPENSTREETMAP_ZIPTOWN_CRON_SECONDS seconds (default 50)
	 *
	 *  @return	int		0 if OK, <>0 if KO (as expected by the scheduled jobs)
	 */
	public function runQueue()
	{
		global $langs;

		dol_include_once('/openstreetmap/lib/ziptownimport.lib.php');
		$langs->load("openstreetmap@openstreetmap");

		$jobs = openstreetmap_ziptown_run_queue($this->db, getDolGlobalInt('OPENSTREETMAP_ZIPTOWN_CRON_SECONDS', 50), 1);
		$lines = array();
		$error = 0;
		foreach ($jobs as $job) {
			if ($job['status'] == 'error') {
				$error++;
			}
			$lines[] = $job['countrycode'].' '.$job['name'].': '.openstreetmap_ziptown_job_percent($job).'% ('.$job['done'].'/'.$job['lines'].', +'.$job['added'].') '.$job['status'];
		}
		$this->output = count($lines) ? implode("\n", $lines) : $langs->trans("OpenStreetMapZiptownQueueEmpty");
		if ($error) {
			$this->error = $this->output;
			return 1;
		}
		return 0;
	}
}
