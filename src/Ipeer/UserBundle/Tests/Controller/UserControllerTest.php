<?php

namespace Ipeer\UserBundle\Tests\Controller;

use Ipeer\ApiUtilityBundle\Test\JSONTestCase;
use Ipeer\UserBundle\DataFixtures\ORM\LoadUserData;
use Ipeer\UserBundle\Entity\User;

class UserControllerTest extends JSONTestCase
{

    /*
     * =============================================
     * Fixtures to load and helper functions
     * =============================================
     */

    private $standardSampleDate = array(
        'Ipeer\UserBundle\DataFixtures\ORM\LoadUserData',
    );

    private $maxLoopLimit = 3; // just loop through 3 users at most in any case

    /**
     * @param User $user
     * @param User[] $userArray
     */
    private function assertUserEquals(User $user, $userArray)
    {
        $this->assertEquals($user->getFirstName(), $userArray['first_name']);
        $this->assertEquals($user->getLastName(), $userArray['last_name']);
        $this->assertEquals($user->getEmail(), $userArray['email']);
    }

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
        $users = LoadUserData::getUsers();

        $this->assertTrue(count($users) > 0,
            'LoadUserData::$users not being loaded properly. Will affect other tests');

        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('user'))["users"];
        $this->assertCount(count($users), $response);

        for($i = 0; $i < $this->maxLoopLimit; $i++) {
            $this->assertUserEquals($users[$i], $response[$i]);
        }
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

    }

    public function testShowAction() {
        $this->loadFixtures($this->standardSampleDate);
        $users = LoadUserData::getUsers();

        for($i = 0; $i < $this->maxLoopLimit; $i++) {
            $route =  $this->getUrl('user_show', array('id' => $i+1));
            $data = $this->getAndTestJSONResponseFrom("GET", $route);
            $this->assertUserEquals($users[$i], $data);
        }
    }

    public function testShowActionInvalid() {
        $this->loadFixtures($this->standardSampleDate);

        $route =  $this->getUrl('user_show', array('id' => 0));
        $this->getAndTestJSONResponseFrom("GET", $route, '', 404);

        $route =  $this->getUrl('user_show', array('id' => count(LoadUserData::getUsers()) * 2));
        $this->getAndTestJSONResponseFrom("GET", $route, '', 404);
    }

    public function testUpdateAction() {
        $this->loadFixtures($this->standardSampleDate);
        $users = LoadUserData::getUsers();

        $route =  $this->getUrl('user_update', array('id' => 1));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"id": 1, "first_name": "Update1", "last_name": "Action Test", "email": "testcreateaction@ipeer.ubc"}');
        $route =  $this->getUrl('user_update', array('id' => 2));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"first_name": "Update2", "last_name": "ActionTwo", "email": "testcreateaction2@ipeer.ubc"}');
        $route =  $this->getUrl('user_update', array('id' => 3));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"first_name": "Update3", "last_name": "ActionThree", "email": "testcreateaction3@ipeer.ubc"}');

        $route =  $this->getUrl('user');
        $response = $this->getAndTestJSONResponseFrom('GET', $route);
        $data = $response["users"];
        $this->assertCount(3, $data); //still 3 users; new ones should not have been created

        for($i = 1; $i <= count($users); $i++) {
            $this->assertEquals($data[$i-1]["first_name"], "Update".$i);
        }
    }

    public function testUpdateActionInvalid() {
        $this->loadFixtures($this->standardSampleDate);
        $users = LoadUserData::getUsers();

        $route =  $this->getUrl('user_update', array('id' => 1));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"id": 2, "last_name": "Action Test", "email": "testcreateaction@ipeer.ubc"}', 400);
        $route =  $this->getUrl('user_update', array('id' => 2));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"id": 4, "first_name": "Update2", "email": "testcreateaction2@ipeer.ubc"}', 400);
        $route =  $this->getUrl('user_update', array('id' => 3));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"id": 1, "first_name": "Update3", "last_name": "ActionThree"}', 400);

        $route =  $this->getUrl('user');
        $response = $this->getAndTestJSONResponseFrom('GET', $route);
        $data = $response["users"];
        $this->assertCount(3, $data); //still 3 users; new ones should not have been created

        for($i = 1; $i < $this->maxLoopLimit; $i++) {
            $this->assertEquals($data[$i]["first_name"], $users[$i]->getFirstName());
        }
    }

    public function testDeleteAction() {
        $this->loadFixtures($this->standardSampleDate);

        $route =  $this->getUrl('user_delete', array('id' => 1));
        $this->getAndTestJSONResponseFrom('DELETE', $route, '', 204);

        $route =  $this->getUrl('user_delete', array('id' => 2));
        $this->getAndTestJSONResponseFrom('DELETE', $route, '', 204);

        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('user'));
        $this->assertCount(1, $response["users"]);

        $route =  $this->getUrl('user_update', array('id' => 1));
        $this->getAndTestJSONResponseFrom("GET", $route, '', 404);

        $route =  $this->getUrl('user_update', array('id' => 2));
        $this->getAndTestJSONResponseFrom("GET", $route, '', 404);
    }

    public function testDeleteActionInvalid() {

    }

}
