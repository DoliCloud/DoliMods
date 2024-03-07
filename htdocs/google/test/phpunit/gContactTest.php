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
        $result = getURLContent('https://people.googleapis.com/v1/contactGroups', 'GET', '', 0, $addheaderscurl);
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

		// Test if third party exists on google contact
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
        $fund = getURLContent('https://people.googleapis.com/v1/'.$contactID.'?personFields='.$personFields, 'GET', '', 0, $addheaderscurl);

		$this->assertEquals($fund['http_code'], 200);

		$ID = json_decode($fund['content'])->resourceName;
		$firstName = json_decode($fund['content'])->names[0]['givenName'];
		$lastName = json_decode($fund['content'])->names[0]['familyName'];
		$email = json_decode($fund['content'])->emailAddresses[0]['value'];
		$i = 0;
		while (json_decode($fund['content'])->phoneNumbers[$i]['type'] != 'mobile' && $i < 10) {
			$i++;
		}
		$this->assertEquals(json_decode($fund['content'])->phoneNumbers[$i]['type'], 'mobile');
		$phone = json_decode($fund['content'])->phoneNumbers[$i]['value'];

		$this->assertEquals($firstName, 'Joliprenom');
		$this->assertEquals($lastName, 'Jolinom');
		$this->assertEquals($email, 'adresse@mail.com');
		$this->assertEquals($phone, '1123581321');

		print __METHOD__." google contact found:".$firstName." ".$lastName." ".$email." ".$phone."\n";

		// Test if update properly
		$object->firstname = 'Joliprenom2';
		$object->lastname = 'Jolinom2';
		$object->email = 'adresse2@mail.com';
		$object->phone_mobile = '1235813212';
		$result = $object->update($object->id, $user);
		$this->assertLessThan($result, 0);
		print __METHOD__." updated:".$result."\n";

		// Test if infos are corrects on google contact
		$fund = getURLContent('https://people.googleapis.com/v1/'.$contactID.'?personFields='.$personFields, 'GET', '', 0, $addheaderscurl);
		$this->assertEquals($fund['http_code'], 200);

		$ID = json_decode($fund['content'])->resourceName;
		$firstName = json_decode($fund['content'])->names[0]['givenName'];
		$lastName = json_decode($fund['content'])->names[0]['familyName'];
		$email = json_decode($fund['content'])->emailAddresses[0]['value'];
		$i = 0;
		while (json_decode($fund['content'])->phoneNumbers[$i]['type'] != 'mobile' && $i < 10) {
			$i++;
		}
		$this->assertEquals(json_decode($fund['content'])->phoneNumbers[$i]['type'], 'mobile');
		$phone = json_decode($fund['content'])->phoneNumbers[$i]['value'];

		$this->assertEquals($firstName, 'Joliprenom2');
		$this->assertEquals($lastName, 'Jolinom2');
		$this->assertEquals($email, 'adresse2@mail.com');
		$this->assertEquals($phone, '1235813212');

		print __METHOD__." google contact updated:".$firstName." ".$lastName." ".$email." ".$phone."\n";

		// Test if delete properly
		$result = $object->delete($object->id, $user);
		$this->assertLessThan($result, 0);
		print __METHOD__." deleted:".$result."\n";

		// Test if third party is not on google contact anymore
		$fund = getURLContent('https://people.googleapis.com/v1/'.$contactID.'?personFields='.$personFields, 'GET', '', 0, $addheaderscurl);
		$this->assertEquals($fund['http_code'], 404);

		print __METHOD__." google contact deleted\n";
    }


    /**
     * testApiCreateUpdateDeleteContact
     *
     * @return void
     */
	public function testApiCreateUpdateDeleteContact() {
		global $conf,$user,$langs,$db;
        require_once dirname(__FILE__).'/../../../../../htdocs/contact/class/contact.class.php';
        $object = new Contact($db);
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

		// Test if third party exists on google contact
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
        $fund = getURLContent('https://people.googleapis.com/v1/'.$contactID.'?personFields='.$personFields, 'GET', '', 0, $addheaderscurl);

		$this->assertEquals($fund['http_code'], 200);

		$firstName = json_decode($fund['content'])->names[0]['givenName'];
		$lastName = json_decode($fund['content'])->names[0]['familyName'];
		$email = json_decode($fund['content'])->emailAddresses[0]['value'];
		$i = 0;
		while (json_decode($fund['content'])->phoneNumbers[$i]['type'] != 'mobile' && $i < 10) {
			$i++;
		}
		$this->assertEquals(json_decode($fund['content'])->phoneNumbers[$i]['type'], 'mobile');
		$phone = json_decode($fund['content'])->phoneNumbers[$i]['value'];

		$this->assertEquals($firstName, 'Joliprenom');
		$this->assertEquals($lastName, 'Jolinom');
		$this->assertEquals($email, 'adresse@mail.com');
		$this->assertEquals($phone, '1123581321');

		print __METHOD__." google contact found:".$firstName." ".$lastName." ".$email." ".$phone."\n";

		// Test if update properly
		$object->firstname = 'Joliprenom2';
		$object->lastname = 'Jolinom2';
		$object->email = 'adresse2@mail.com';
		$object->phone_mobile = '1235813212';
		$result = $object->update($object->id, $user);
		$this->assertLessThan($result, 0);
		print __METHOD__." updated:".$result."\n";

		// Test if infos are corrects on google contact
		$fund = getURLContent('https://people.googleapis.com/v1/'.$contactID.'?personFields='.$personFields, 'GET', '', 0, $addheaderscurl);
		$this->assertEquals($fund['http_code'], 200);

		$firstName = json_decode($fund['content'])->names[0]['givenName'];
		$lastName = json_decode($fund['content'])->names[0]['familyName'];
		$email = json_decode($fund['content'])->emailAddresses[0]['value'];
		$i = 0;
		while (json_decode($fund['content'])->phoneNumbers[$i]['type'] != 'mobile' && $i < 10) {
			$i++;
		}
		$this->assertEquals(json_decode($fund['content'])->phoneNumbers[$i]['type'], 'mobile');
		$phone = json_decode($fund['content'])->phoneNumbers[$i]['value'];

		$this->assertEquals($firstName, 'Joliprenom2');
		$this->assertEquals($lastName, 'Jolinom2');
		$this->assertEquals($email, 'adresse2@mail.com');
		$this->assertEquals($phone, '1235813212');

		print __METHOD__." google contact updated:".$firstName." ".$lastName." ".$email." ".$phone."\n";

		// Test if delete properly
		$result = $object->delete($user);
		$this->assertLessThan($result, 0);
		print __METHOD__." deleted:".$result.$object->ref_ext."\n";

		// Test if third party is not on google contact anymore
		$fund = getURLContent('https://people.googleapis.com/v1/'.$contactID.'?personFields='.$personFields, 'GET', '', 0, $addheaderscurl);
		$this->assertEquals($fund['http_code'], 404);

		print __METHOD__." google contact deleted\n";
	}

    /**
     * testApiCreateUpdateDeleteMember
     *
     * @return void
     */
	public function testApiCreateUpdateDeleteMember() {
		global $conf,$user,$langs,$db;
        require_once dirname(__FILE__).'/../../../../../htdocs/adherents/class/adherent.class.php';
        $object = new Adherent($db);
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

		// Test if third party exists on google contact
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
        $fund = getURLContent('https://people.googleapis.com/v1/'.$contactID.'?personFields='.$personFields, 'GET', '', 0, $addheaderscurl);

		$this->assertEquals($fund['http_code'], 200);

		$firstName = json_decode($fund['content'])->names[0]['givenName'];
		$lastName = json_decode($fund['content'])->names[0]['familyName'];
		$email = json_decode($fund['content'])->emailAddresses[0]['value'];
		$i = 0;
		while (json_decode($fund['content'])->phoneNumbers[$i]['type'] != 'mobile' && $i < 10) {
			$i++;
		}
		$this->assertEquals(json_decode($fund['content'])->phoneNumbers[$i]['type'], 'mobile');
		$phone = json_decode($fund['content'])->phoneNumbers[$i]['value'];

		$this->assertEquals($firstName, 'Joliprenom');
		$this->assertEquals($lastName, 'Jolinom');
		$this->assertEquals($email, 'adresse@mail.com');
		$this->assertEquals($phone, '1123581321');

		print __METHOD__." google contact found:".$firstName." ".$lastName." ".$email." ".$phone."\n";

		// Test if update properly
		$object->firstname = 'Joliprenom2';
		$object->lastname = 'Jolinom2';
		$object->email = 'adresse2@mail.com';
		$object->phone_mobile = '1235813212';
		$result = $object->update($user);
		$this->assertLessThan($result, 0);
		print __METHOD__." updated:".$result."\n";

		// Test if infos are corrects on google contact
		$fund = getURLContent('https://people.googleapis.com/v1/'.$contactID.'?personFields='.$personFields, 'GET', '', 0, $addheaderscurl);
		$this->assertEquals($fund['http_code'], 200);

		$firstName = json_decode($fund['content'])->names[0]['givenName'];
		$lastName = json_decode($fund['content'])->names[0]['familyName'];
		$email = json_decode($fund['content'])->emailAddresses[0]['value'];
		$i = 0;
		while (json_decode($fund['content'])->phoneNumbers[$i]['type'] != 'mobile' && $i < 10) {
			$i++;
		}
		$this->assertEquals(json_decode($fund['content'])->phoneNumbers[$i]['type'], 'mobile');
		$phone = json_decode($fund['content'])->phoneNumbers[$i]['value'];

		$this->assertEquals($firstName, 'Joliprenom2');
		$this->assertEquals($lastName, 'Jolinom2');
		$this->assertEquals($email, 'adresse2@mail.com');
		$this->assertEquals($phone, '1235813212');

		print __METHOD__." google contact updated:".$firstName." ".$lastName." ".$email." ".$phone."\n";

		// Test if delete properly
		$result = $object->delete($user);
		$this->assertLessThan($result, 0);
		print __METHOD__." deleted:".$result.$object->ref_ext."\n";

		// Test if third party is not on google contact anymore
		$fund = getURLContent('https://people.googleapis.com/v1/'.$contactID.'?personFields='.$personFields, 'GET', '', 0, $addheaderscurl);
		$this->assertEquals($fund['http_code'], 404);

		print __METHOD__." google contact deleted\n";
	}


    /**
     * testApiLinkUnlinkDeleteTagThirdParty
     *
     * @return void
     */
	public function testApiLinkUnlinkDeleteTagThirdParty() {

		global $conf, $user, $langs, $db;


		// Create third party
        require_once dirname(__FILE__).'/../../../../../htdocs/societe/class/societe.class.php';
		$object = new Societe($db);
		$object->initAsSpecimen();

		$created = $object->create($user);
		$this->assertLessThan($created, 0);
		print __METHOD__." third party created:".$object->id."\n";

		// Create category
		require_once dirname(__FILE__).'/../../../../../htdocs/categories/class/categorie.class.php';
		$categ = new Categorie($db);
		$categ->initAsSpecimen();
		$created = $categ->create($user);
		$this->assertLessThan($created, 0);
		print __METHOD__." category created:".$categ->id."\n";

		// Link third party to category
		$result = $categ->add_type($object, 'supplier');
		$this->assertLessThan($result, 0);
		print __METHOD__." third party linked to category:".$result."\n";

		// See if we can find group in google contact
		$groupID = $categ->ref_ext;
		if (!empty($groupID) && preg_match('/google:(contactGroups\/.*)/', $groupID, $reg)) {
			$groupID = $reg[1];
		}
		$this->assertNotEquals($groupID, '');


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

        $fund = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'GET', '', 0, $addheaderscurl);

		$this->assertEquals($fund['http_code'], 200);
		print __METHOD__." group found\n";

		// See if group contains one member
		$memberCount = json_decode($fund['content'])->memberCount;
		$this->assertEquals($memberCount, 1);

		// Remove thirdparty from group and see if group is empty
		$result = $categ->del_type($object, 'supplier');
		$this->assertLessThan($result, 0);
		print __METHOD__." third party unlinked from category:".$result."\n";
		$fund = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'GET', '', 0, $addheaderscurl);
		$this->assertEquals($fund['http_code'], 200);
		print __METHOD__." group updated\n";
		$memberCount = json_decode($fund['content'])->memberCount;
		$this->assertEquals($memberCount, 0);
		print __METHOD__." group empty\n";

		// Readd and delete thirdparty from group and see if group is empty
		$result = $categ->add_type($object, 'supplier');
		$this->assertLessThan($result, 0);
		print __METHOD__." third party re-linked to category:".$result."\n";

		$deleted = $object->delete($object->id, $user);
		$this->assertLessThan($deleted, 0);
		print __METHOD__." third party deleted:".$deleted."\n";

		$fund = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'GET', '', 0, $addheaderscurl);
		$this->assertEquals($fund['http_code'], 200);
		print __METHOD__." group updated\n";

		$memberCount = json_decode($fund['content'])->memberCount;
		$this->assertEquals($memberCount, 0);
		print __METHOD__." group empty\n";

		// Delete category
		$deleted = $categ->delete($user);
		$this->assertLessThan($deleted, 0);
		print __METHOD__." category deleted:".$deleted."\n";

		$fund = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'GET', '', 0, $addheaderscurl);
		$this->assertEquals($fund['http_code'], 404);
		print __METHOD__." group deleted\n";
	}

	/**
	 * testApiLinkUnlinkDeleteTagContact
	 *
	 * @return void
	 */
	public function testApiLinkUnlinkDeleteTagContact() {

		global $conf, $user, $langs, $db;


		// Create contact
        require_once dirname(__FILE__).'/../../../../../htdocs/contact/class/contact.class.php';
		$object = new Contact($db);
		$object->initAsSpecimen();

		$created = $object->create($user);
		$this->assertLessThan($created, 0);
		print __METHOD__." contact created:".$object->id."\n";

		// Create category
		require_once dirname(__FILE__).'/../../../../../htdocs/categories/class/categorie.class.php';
		$categ = new Categorie($db);
		$categ->initAsSpecimen();
		$created = $categ->create($user);
		$this->assertLessThan($created, 0);
		print __METHOD__." category created:".$categ->id."\n";

		// Link contact to category
		$result = $categ->add_type($object, 'contact');
		$this->assertLessThan($result, 0);
		print __METHOD__." contact linked to category:".$result."\n";

		// See if we can find group in google contact
		$groupID = $categ->ref_ext;
		if (!empty($groupID) && preg_match('/google:(contactGroups\/.*)/', $groupID, $reg)) {
			$groupID = $reg[1];
		}
		$this->assertNotEquals($groupID, '');


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

        $fund = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'GET', '', 0, $addheaderscurl);

		$this->assertEquals($fund['http_code'], 200);
		print __METHOD__." group found\n";

		// See if group contains one member
		$memberCount = json_decode($fund['content'])->memberCount;
		$this->assertEquals($memberCount, 1);

		// Remove contact from group and see if group is empty
		$result = $categ->del_type($object, 'contact');
		$this->assertLessThan($result, 0);
		print __METHOD__." contact unlinked from category:".$result."\n";
		$fund = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'GET', '', 0, $addheaderscurl);
		$this->assertEquals($fund['http_code'], 200);
		print __METHOD__." group updated\n";
		$memberCount = json_decode($fund['content'])->memberCount;
		$this->assertEquals($memberCount, 0);
		print __METHOD__." group empty\n";

		// Readd and delete contact from group and see if group is empty
		$result = $categ->add_type($object, 'contact');
		$this->assertLessThan($result, 0);
		print __METHOD__." contact re-linked to category:".$result."\n";

		$deleted = $object->delete($user);
		$this->assertLessThan($deleted, 0);
		print __METHOD__." contact deleted:".$deleted."\n";

		$fund = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'GET', '', 0, $addheaderscurl);
		$this->assertEquals($fund['http_code'], 200);
		print __METHOD__." group updated\n";

		$memberCount = json_decode($fund['content'])->memberCount;
		$this->assertEquals($memberCount, 0);
		print __METHOD__." group empty\n";

		// Delete category
		$deleted = $categ->delete($user);
		$this->assertLessThan($deleted, 0);
		print __METHOD__." category deleted:".$deleted."\n";

		$fund = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'GET', '', 0, $addheaderscurl);
		$this->assertEquals($fund['http_code'], 404);
		print __METHOD__." group in google contact deleted\n";
	}

	/**
	 * testApiLinkUnlinkDeleteTagMember
	 *
	 * @return void
	 */
	public function testApiLinkUnlinkDeleteTagMember() {

		global $conf, $user, $langs, $db;


		// Create contact
        require_once dirname(__FILE__).'/../../../../../htdocs/adherents/class/adherent.class.php';
		$object = new Adherent($db);
		$object->initAsSpecimen();

		$created = $object->create($user);
		$this->assertLessThan($created, 0);
		print __METHOD__." member created:".$object->id."\n";

		// Create category
		require_once dirname(__FILE__).'/../../../../../htdocs/categories/class/categorie.class.php';
		$categ = new Categorie($db);
		$categ->initAsSpecimen();
		$created = $categ->create($user);
		$this->assertLessThan($created, 0);
		print __METHOD__." category created:".$categ->id."\n";

		// Link member to category
		$result = $categ->add_type($object, 'member');
		$this->assertLessThan($result, 0);
		print __METHOD__." member linked to category:".$result."\n";

		// See if we can find group in google contact
		$groupID = $categ->ref_ext;
		if (!empty($groupID) && preg_match('/google:(contactGroups\/.*)/', $groupID, $reg)) {
			$groupID = $reg[1];
		}
		$this->assertNotEquals($groupID, '');


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

        $fund = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'GET', '', 0, $addheaderscurl);

		$this->assertEquals($fund['http_code'], 200);
		print __METHOD__." group found\n";

		// See if group contains one member
		$memberCount = json_decode($fund['content'])->memberCount;
		$this->assertEquals($memberCount, 1);

		// Remove member from group and see if group is empty
		$result = $categ->del_type($object, 'member');
		$this->assertLessThan($result, 0);
		print __METHOD__." member unlinked from category:".$result."\n";
		$fund = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'GET', '', 0, $addheaderscurl);
		$this->assertEquals($fund['http_code'], 200);
		print __METHOD__." group updated\n";
		$memberCount = json_decode($fund['content'])->memberCount;
		$this->assertEquals($memberCount, 0);
		print __METHOD__." group empty\n";

		// Readd and delete member from group and see if group is empty
		$result = $categ->add_type($object, 'member');
		$this->assertLessThan($result, 0);
		print __METHOD__." member re-linked to category:".$result."\n";

		$deleted = $object->delete($user);
		$this->assertLessThan($deleted, 0);
		print __METHOD__." member deleted:".$deleted."\n";

		$fund = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'GET', '', 0, $addheaderscurl);
		$this->assertEquals($fund['http_code'], 200);
		print __METHOD__." group updated\n";

		$memberCount = json_decode($fund['content'])->memberCount;
		$this->assertEquals($memberCount, 0);
		print __METHOD__." group empty\n";

		// Delete category
		$deleted = $categ->delete($user);
		$this->assertLessThan($deleted, 0);
		print __METHOD__." category deleted:".$deleted."\n";

		$fund = getURLContent('https://people.googleapis.com/v1/'.$groupID, 'GET', '', 0, $addheaderscurl);
		$this->assertEquals($fund['http_code'], 404);
		print __METHOD__." group in google contact deleted\n";
	}

}
