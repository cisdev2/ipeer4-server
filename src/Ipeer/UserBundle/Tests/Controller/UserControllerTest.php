<?php

namespace Ipeer\UserBundle\Tests\Controller;

use Ipeer\ApiUtilityBundle\Test\IpeerTestCase;
use Ipeer\UserBundle\DataFixtures\ORM\LoadUserData;

class UserControllerTest extends IpeerTestCase
{
    /**
     * @param $userExpected
     * @param $userActual
     */
    private function assertUserEquals($userExpected, $userActual)
    {
        $this->assertEquals($userExpected[0], $userActual['first_name']);
        $this->assertEquals($userExpected[1], $userActual['last_name']);
        $this->assertEquals($userExpected[2], $userActual['email']);
    }

    /*
     * =============================================
     * Valid Action Tests
     * ============================================
     */

    public function testIndexActionEmpty()
    {
        $this->loadFixtures(array());
        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('user'));
        // When no users present, get an empty array
        $this->assertCount(0, $response["users"]);
    }

    /**
     * @depends testIndexActionEmpty
     */
    public function testIndexAction()
    {
        $this->loadFixtures($this->IpeerFixtures);

        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('user'));
        $response = $response["users"];

        $this->assertCount(LoadUserData::NUMBER_OF_USERS, $response);

        $this->assertUserEquals(array("Sudo1", "SuperAdmin01", "sudo01@ipeer.ubc"), $response[0]);
        $this->assertUserEquals(array("APSC", "Instructor01", "apscInstr@ipeer.ubc") , $response[6]);
        $this->assertUserEquals(array("Tutour", "Tutor01", "tutor01@ipeer.ubc"), $response[13]);
        $this->assertUserEquals(array("Kirk", "Student01", "student01@ipeer.ubc"), $response[17]);
    }

    /**
     * @depends testIndexAction
     */
    public function testShowAction()
    {
        $this->assertUserEquals(array("Sudo1", "SuperAdmin01", "sudo01@ipeer.ubc"),
            $this->getAndTestJSONResponseFrom("GET", $this->getUrl('user_show', array('id' => 1))));
        $this->assertUserEquals(array("APSC", "Instructor01", "apscInstr@ipeer.ubc") ,
            $this->getAndTestJSONResponseFrom("GET", $this->getUrl('user_show', array('id' => 7))));
        $this->assertUserEquals(array("Tutour", "Tutor01", "tutor01@ipeer.ubc"),
            $this->getAndTestJSONResponseFrom("GET", $this->getUrl('user_show', array('id' => 14))));
        $this->assertUserEquals(array("Kirk", "Student01", "student01@ipeer.ubc"),
            $this->getAndTestJSONResponseFrom("GET", $this->getUrl('user_show', array('id' => 18))));
    }

    /**
     * @depends testShowAction
     */
    public function testUpdateAction()
    {
        $route =  $this->getUrl('user_update', array('id' => 1));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"id": 1, "first_name": "Update1", "last_name": "Last1", "email": "testcreateaction1@ipeer.ubc"}');
        $route =  $this->getUrl('user_update', array('id' => 10));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"first_name": "Update2", "last_name": "Last2", "email": "testcreateaction2@ipeer.ubc"}');
        $route =  $this->getUrl('user_update', array('id' => 20));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"first_name": "Update3", "last_name": "Last3", "email": "testcreateaction3@ipeer.ubc"}');

        $route =  $this->getUrl('user');
        $response = $this->getAndTestJSONResponseFrom('GET', $route);
        $data = $response["users"];
        $this->assertCount(LoadUserData::NUMBER_OF_USERS, $data); //still same amount of users; new ones should not have been created

        $this->assertUserEquals(array("Update1", "Last1", "testcreateaction1@ipeer.ubc"), $data[1-1]);
        $this->assertUserEquals(array("Update2", "Last2", "testcreateaction2@ipeer.ubc"), $data[10-1]);
        $this->assertUserEquals(array("Update3", "Last3", "testcreateaction3@ipeer.ubc"), $data[20-1]);
    }

    /**
     * @depends testUpdateAction
     */
    public function testCreateAction()
    {
        $route =  $this->getUrl('user');

        $data = $this->getAndTestJSONResponseFrom("POST", $route,
            '{"first_name": "Test User", "last_name": "Action Test", "email": "testcreateaction@ipeer.ubc"}');
        $this->assertUserEquals(array("Test User", "Action Test", "testcreateaction@ipeer.ubc"), $data);
        $this->assertEquals(1 + LoadUserData::NUMBER_OF_USERS, $data['id']);

        $data = $this->getAndTestJSONResponseFrom("POST", $route,
            '{"first_name": "Test2", "last_name": "ActionTwo", "email": "testcreateaction2@ipeer.ubc"}');
        $this->assertUserEquals(array("Test2", "ActionTwo", "testcreateaction2@ipeer.ubc"), $data);
        $this->assertEquals(2 + LoadUserData::NUMBER_OF_USERS, $data['id']);

        $route =  $this->getUrl('user');
        $response = $this->getAndTestJSONResponseFrom("GET", $route);
        $this->assertCount(2 + LoadUserData::NUMBER_OF_USERS, $response["users"]); // the two users we created
    }

    /*
     * No dependency
     */
    public function testDeleteAction()
    {
        $this->loadFixtures($this->IpeerFixtures);

        $route =  $this->getUrl('user_delete', array('id' => 1));
        $this->getAndTestJSONResponseFrom('DELETE', $route, '', 204);

        $route =  $this->getUrl('user_delete', array('id' => 2));
        $this->getAndTestJSONResponseFrom('DELETE', $route, '', 204);

        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('user'));
        $this->assertCount(LoadUserData::NUMBER_OF_USERS-2, $response["users"]); //removed 2 users

        // check that the users truly no longer exist
        $route =  $this->getUrl('user_show', array('id' => 1));
        $this->getAndTestJSONResponseFrom("GET", $route, '', 404);

        $route =  $this->getUrl('user_show', array('id' => 2));
        $this->getAndTestJSONResponseFrom("GET", $route, '', 404);
    }

    /*
     * =============================================
     * Invalid Action Tests
     * =============================================
     */

    public function testShowActionInvalid()
    {
        $this->loadFixtures($this->IpeerFixtures);

        $route =  $this->getUrl('user_show', array('id' => 0));
        $this->getAndTestJSONResponseFrom("GET", $route, '', 404);

        $route =  $this->getUrl('user_show', array('id' => LoadUserData::NUMBER_OF_USERS * 2));
        $this->getAndTestJSONResponseFrom("GET", $route, '', 404);
    }

    /**
     * @depends testShowActionInvalid
     */
    public function testUpdateActionInvalid()
    {
        // various missing data examples
        $route =  $this->getUrl('user_update', array('id' => 1));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"id": 2, "last_name": "Action Test", "email": "testcreateaction@ipeer.ubc"}', 400);
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"id": 2, "first_name": "", "last_name": "Action Test", "email": "testcreateaction@ipeer.ubc"}', 400);
        $this->getAndTestJSONResponseFrom('POST', $route,
            '', 400);
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{}', 400);
        $route =  $this->getUrl('user_update', array('id' => 2));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"id": 4, "first_name": "Update2", "email": "testcreateaction2@ipeer.ubc"}', 400);
        $route =  $this->getUrl('user_update', array('id' => 3));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"id": 1, "first_name": "Update3", "last_name": "ActionThree"}', 400);

        // valid and invalid updates to a non-existent entity should 404
        $route =  $this->getUrl('user_update', array('id' => LoadUserData::NUMBER_OF_USERS * 2));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"id": 1, "first_name": "Update3", "last_name": "ActionThree"}', 404);
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"first_name": "Update3", "last_name": "ActionThree", "email": "testcreateaction3@ipeer.ubc"}', 404);

        $route =  $this->getUrl('user');
        $response = $this->getAndTestJSONResponseFrom('GET', $route);
        $response = $response["users"];
        $this->assertCount(LoadUserData::NUMBER_OF_USERS, $response); //still same amount of users; new ones should not have been created

        $this->assertUserEquals(array("Sudo1", "SuperAdmin01", "sudo01@ipeer.ubc"), $response[0]);
        $this->assertUserEquals(array("APSC", "Instructor01", "apscInstr@ipeer.ubc") , $response[6]);
        $this->assertUserEquals(array("Tutour", "Tutor01", "tutor01@ipeer.ubc"), $response[13]);
        $this->assertUserEquals(array("Kirk", "Student01", "student01@ipeer.ubc"), $response[17]);
    }

    /**
     * @depends testUpdateActionInvalid
     */
    public function testCreateActionInvalid()
    {
        $route =  $this->getUrl('user');

        // various corruptions of data (blank, empty object, bad email, missing various fields)
        $this->getAndTestJSONResponseFrom("POST", $route,
            '', 400);
        $this->getAndTestJSONResponseFrom("POST", $route,
            '{}', 400);
        $this->getAndTestJSONResponseFrom("POST", $route,
            '{"last_name": "Action Test", "email": "testcreateaction@ipeer.ubc"}', 400);
        $this->getAndTestJSONResponseFrom("POST", $route,
            '{"first_name": "Action Test", "email": "testcreateaction@ipeer.ubc"}', 400);
        $this->getAndTestJSONResponseFrom("POST", $route,
            '{"first_name": "Action Test", "last_name": "Action Test"}', 400);
        $this->getAndTestJSONResponseFrom("POST", $route,
            '{"first_name": "Action Test", "last_name": "Action Test", "email": "testcreateaction"}', 400);
        $this->getAndTestJSONResponseFrom("POST", $route,
            '{"first_name": "Action Test", "last_name": "Action Test", "email": "testcreateaction@"}', 400);
        $this->getAndTestJSONResponseFrom("POST", $route,
            '{"first_name": "Action Test", "last_name": "Action Test", "email": "testcreateaction@com"}', 400);

        $response = $this->getAndTestJSONResponseFrom("GET", $route);
        $this->assertCount(LoadUserData::NUMBER_OF_USERS, $response["users"]); // no users created
    }

    /**
     * @depends testCreateActionInvalid
     */

    public function testDeleteActionInvalid()
    {
        // users that don't exist in the first place should 404
        $route =  $this->getUrl('user_delete', array('id' => LoadUserData::NUMBER_OF_USERS * 2 ));
        $this->getAndTestJSONResponseFrom('DELETE', $route, '', 404);

        $route =  $this->getUrl('user_delete', array('id' => 0));
        $this->getAndTestJSONResponseFrom('DELETE', $route, '', 404);
    }

}
