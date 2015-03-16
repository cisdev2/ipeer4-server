<?php

namespace Ipeer\UserBundle\Tests\Controller;


use Ipeer\ApiUtilityBundle\Test\JSONTestCase;
use Ipeer\UserBundle\DataFixtures\ORM\LoadUserData;

class UserControllerTest extends JSONTestCase
{

    /*
     * =============================================
     * Fixtures to load in this test
     * =============================================
     */

    private $standardSampleDate = array('Ipeer\UserBundle\DataFixtures\ORM\LoadUserData');

    /*
     * =============================================
     * User Controller Tests
     * =============================================
     */

    public function testIndexActionEmpty() {
        $this->loadFixtures(array());
        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('user'));
        $this->assertCount(0, $response["users"]);
    }

    public function testIndexAction() {
        $this->loadFixtures($this->standardSampleDate);
        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('user'));
        $this->assertCount(3, $response["users"]);
    }

    public function testCreateAction() {
        $this->loadFixtures(array());

        $route =  $this->getUrl('user');

        $data = $this->getAndTestJSONResponseFrom("POST", $route,
            '{"first_name": "Test User", "last_name": "Action Test", "email": "testcreateaction@ipeer.ubc"}');
        $this->assertEquals(1, $data['id']);
        $this->assertEquals("Test User", $data['first_name']);
        $this->assertEquals("Action Test", $data['last_name']);
        $this->assertEquals("testcreateaction@ipeer.ubc", $data['email']);

        $data = $this->getAndTestJSONResponseFrom("POST", $route,
            '{"first_name": "Test2", "last_name": "ActionTwo", "email": "testcreateaction2@ipeer.ubc"}');
        $this->assertEquals(2, $data['id']);
        $this->assertEquals("Test2", $data['first_name']);
        $this->assertEquals("ActionTwo", $data['last_name']);
        $this->assertEquals("testcreateaction2@ipeer.ubc", $data['email']);

        $route =  $this->getUrl('user');
        $response = $this->getAndTestJSONResponseFrom("GET", $route);
        $this->assertCount(2, $response["users"]);
    }

    public function testCreateActionInvalid() {
        $route =  $this->getUrl('user');

        $this->getAndTestJSONResponseFrom("POST", $route,
            '{"last_name": "Action Test", "email": "testcreateaction@ipeer.ubc"}', 400);

    }

    public function testShowAction() {
        $this->loadFixtures($this->standardSampleDate);

        for($i = 1; $i <= count(LoadUserData::$users); $i++) {
            $route =  $this->getUrl('user_show', array('id' => $i));
            $response = $this->getAndTestJSONResponseFrom("GET", $route);
            $data = $response;
            $this->assertEquals(LoadUserData::$users[$i-1]->getFirstName(), $data['first_name']);
            $this->assertEquals(LoadUserData::$users[$i-1]->getLastName(), $data['last_name']);
            $this->assertEquals(LoadUserData::$users[$i-1]->getEmail(), $data['email']);
        }
    }

    public function testUpdateAction() {
        $this->loadFixtures($this->standardSampleDate);

        $route =  $this->getUrl('user_update', array('id' => 1));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"id": 1, "first_name": "Update1", "last_name": "Action Test", "email": "testcreateaction@ipeer.ubc"}');
        $route =  $this->getUrl('user_update', array('id' => 2));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"id": 2, "first_name": "Update2", "last_name": "ActionTwo", "email": "testcreateaction2@ipeer.ubc"}');
        $route =  $this->getUrl('user_update', array('id' => 3));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"id": 3, "first_name": "Update3", "last_name": "ActionThree", "email": "testcreateaction3@ipeer.ubc"}');

        $route =  $this->getUrl('user');
        $response = $this->getAndTestJSONResponseFrom('GET', $route);
        $data = $response["users"];
        $this->assertCount(3, $data); //still 3 users; new ones should not have been created

        $this->assertEquals($data[0]["first_name"], "Update1");
        $this->assertEquals($data[1]["first_name"], "Update2");
        $this->assertEquals($data[2]["first_name"], "Update3");

    }

    public function testDeleteAction() {
        $this->loadFixtures($this->standardSampleDate);

        $route =  $this->getUrl('user_delete', array('id' => 1));
        $this->getAndTestJSONResponseFrom('DELETE', $route, '', 204);

        $route =  $this->getUrl('user_delete', array('id' => 2));
        $this->getAndTestJSONResponseFrom('DELETE', $route, '', 204);

        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('user'));
        $this->assertCount(1, $response["users"]);

    }

}
