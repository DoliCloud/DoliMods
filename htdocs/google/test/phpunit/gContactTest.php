<?php
/* Copyright (C) 2022 Faustin Boitel  <fboitel@enseirb-matmeca.fr>
 *
 * This program is free software; you can redistribute it and/or modify
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
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 * or see https://www.gnu.org/
 */

/**
 *      \file       test/phpunit/gContactTest.php
 *      \ingroup    test
 *      \brief      PHPUnit test
 *      \remarks    To run this script as CLI:  phpunit filename.php
 */

global $conf,$user,$langs,$db;
//define('TEST_DB_FORCE_TYPE','mysql');	// This is to force using mysql driver
//require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__).'/../../../../../htdocs/master.inc.php';
require_once dirname(__FILE__).'/../../lib/google_contact.lib.php';

if (empty($user->id)) {
	print "Load permissions for admin user nb 1\n";
	$user->fetch(1);
	$user->getrights();
}
$conf->global->MAIN_DISABLE_ALL_MAILS=1;


/**
 * Class for PHPUnit tests
 *
 * @backupGlobals disabled
 * @backupStaticAttributes enabled
 * @remarks backupGlobals must be disabled to have db,conf,user and lang not erased.
 */
class gContactTest extends PHPUnit\Framework\TestCase
{
	protected $savconf;
	protected $savuser;
	protected $savlangs;
	protected $savdb;

	/**
	 * Constructor
	 * We save global variables into local variables
	 *
	 * @return AdminLibTest
	 */
	public function __construct()
	{
		parent::__construct();

		//$this->sharedFixture
		global $conf,$user,$langs,$db;
		$this->savconf=$conf;
		$this->savuser=$user;
		$this->savlangs=$langs;
		$this->savdb=$db;

		print __METHOD__." db->type=".$db->type." user->id=".$user->id;
		//print " - db ".$db->db;
		print "\n";
	}

	/**
	 * setUpBeforeClass
	 *
	 * @return void
	 */
	public static function setUpBeforeClass()
	{
		global $conf,$user,$langs,$db;

        if (!isModEnabled('google')) {
			print __METHOD__." module google must be enabled.\n"; die(1);
		}

		$db->begin(); // This is to have all actions inside a transaction even if test launched without suite.

		print __METHOD__."\n";
	}

	/**
	 * tearDownAfterClass
	 *
	 * @return	void
	 */
	public static function tearDownAfterClass()
	{
		global $conf,$user,$langs,$db;
		$db->rollback();

		print __METHOD__."\n";
	}

	/**
	 * Init phpunit tests
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		global $conf,$user,$langs,$db;
		$conf=$this->savconf;
		$user=$this->savuser;
		$langs=$this->savlangs;
		$db=$this->savdb;

		print __METHOD__."\n";
	}
	/**
	 * End phpunit tests
	 *
	 * @return	void
	 */
	protected function tearDown()
	{
		print __METHOD__."\n";
	}


    /**
     * testApiConnnection
     *
     * @return void
     */
    public function testApiConnnection() {

		global $conf,$user,$langs,$db;

        require_once dirname(__FILE__).'/../../lib/google_calendar.lib.php';
        // Create client/token object
	    $key_file_location = $conf->google->multidir_output[$conf->entity]."/".(!empty($conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY)?$conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY:"");
	    $force_do_not_use_session=false; // by default
        $client=getTokenFromServiceAccount(!empty($conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL)?$conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL:"", $key_file_location, $force_do_not_use_session, 'web');


        // Test token
        $this->assertEquals(is_array($client), true);
		print __METHOD__." result=".is_array($client)."\n";

        // Test a connection

        if (is_array($client['google_web_token']) && key_exists('access_token', $client['google_web_token'])) {
            $access_token=$client['google_web_token']['access_token'];
        } else {
            $tmp=json_decode($client['google_web_token']);
            $access_token=$tmp->access_token;
        }
        $addheaderscurl=array('Content-Type: application/json','GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'If-Match: *');
        $result = getURLContent('https://people.googleapis.com/v1/contactGroups', 'GET', array(), 0, $addheaderscurl);
        $this->assertEquals($result['http_code'], 200);
        print __METHOD__." connection try, http_code:".$result['http_code']."\n";

    }

    /**
     * testApiCreateUpdateDeleteThirdParty
     *
     *
     * @return void
     */
    public function testApiCreateUpdateDeleteThirdParty() {


        global $conf,$user,$langs,$db;
        require_once dirname(__FILE__).'/../../../../../htdocs/societe/class/societe.class.php';
        $object = new Societe($db);
        $object->initAsSpecimen();
		$object->firstname = 'Joliprenom';
		$object->lastname = 'Jolinom';
		$object->email = 'adresse@mail.com';
		$object->phone_mobile = '1123581321';

        $created=$object->create($user);
		// Test if Thirdparty was created properly
        $this->assertLessThan($created, 0);
        print __METHOD__." created:".$result."\n";

		// Get contact
		$contactID = $object->ref_ext;
		if ($contactID && preg_match('/google:(people\/.*)/', $contactID, $reg)) {
			$contactID = $reg[1];
		}
		$this->assertNotEquals($contactID, '');
		print __METHOD__." google contact ID:".$contactID."\n";

		// Assure that the thirdparty is in the google contact
		// Create client/token object
		$key_file_location = $conf->google->multidir_output[$conf->entity]."/".(!empty($conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY)?$conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY:"");
		$force_do_not_use_session=false; // by default
		$client=getTokenFromServiceAccount(!empty($conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL)?$conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL:"", $key_file_location, $force_do_not_use_session, 'web');

		if (is_array($client['google_web_token']) && key_exists('access_token', $client['google_web_token'])) {
			$access_token=$client['google_web_token']['access_token'];
		} else {
			$tmp=json_decode($client['google_web_token']);
			$access_token=$tmp->access_token;
		}
		$addheaderscurl=array('Content-Type: application/json','GData-Version: 3.0', 'Authorization: Bearer '.$access_token, 'If-Match: *');

		$personFields = "emailAddresses,names,phoneNumbers";
        $fund = getURLContent('https://people.googleapis.com/v1/'.$contactID.'?personFields='.$personFields, 'GET', array(), 0, $addheaderscurl);

		$this->assertEquals($fund['http_code'], 200);

		$firstName = json_decode($fund['content'])->names[0]['givenName'];
		$lastName = json_decode($fund['content'])->names[0]['familyName'];
		$email = json_decode($fund['content'])->emailAddresses[0]['value'];
		$phone = json_decode($fund['content'])->phoneNumbers[0]['value'];
		$this->assertEquals($firstName, 'Joliprenom');
		$this->assertEquals($lastName, 'Jolinom');
		$this->assertEquals($email, 'adresse@mail.com');
		$this->assertEquals($phone, '1123581321');

		print __METHOD__." google contact found:".$firstName." ".$lastName." ".$email." ".$phone."\n";

    }


    /**
     * testApiCreateUpdateDeleteContact
     *
     * @return void
     */

    /**
     * testApiCreateUpdateDeleteMember
     *
     * @return void
     */

    /**
     * testApiLinkToTag
     *
     * @return void
     */

    /**
     * testApiUnlinkToTag
     *
     * @return void
     */

    /**
     * testApiDeleteTag
     *
     * @return void
     */


}
