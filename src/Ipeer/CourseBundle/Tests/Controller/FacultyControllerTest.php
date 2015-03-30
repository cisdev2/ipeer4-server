<?php

namespace Ipeer\CourseBundle\Tests\Controller;

use Ipeer\ApiUtilityBundle\Test\IpeerTestCase;
use Ipeer\CourseBundle\DataFixtures\ORM\LoadFacultyData;
use Ipeer\UserBundle\DataFixtures\ORM\LoadUserData;

class FacultyControllerTest extends IpeerTestCase
{
    /*
     * =============================================
     * Valid Action Tests
     * =============================================
     */

    public function testIndexActionEmpty()
    {
        $this->loadFixtures(array());
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('faculty'));
        $this->assertCount(0, $response['faculties']);
    }

    /**
     * @depends testIndexActionEmpty
     */
    public function testIndexAction()
    {
        $this->loadFixtures($this->IpeerFixtures);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('faculty'));
        $response = $response['faculties'];
        $this->assertCount(LoadFacultyData::NUMBER_OF_FACULTIES, $response);
        $this->assertEquals("Applied Science", $response[0]['name']);
        $this->assertEquals("Science", $response[1]['name']);
        $this->assertEquals("Business", $response[2]['name']);
        $this->assertEquals("Arts", $response[3]['name']);
    }

    /**
     * @depends testIndexAction
     */
    public function testShowAction()
    {
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('faculty_show', array('id' => 1)));
        $this->assertEquals("Applied Science", $response['name']);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('faculty_show', array('id' => 4)));
        $this->assertEquals("Arts", $response['name']);
    }

    /**
     * @depends testShowAction
     */
    public function testUpdateAction()
    {
        $response = $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('faculty_update', array('id' => 1)),'{"name":"AppSci"}');
        $this->assertEquals("AppSci", $response['name']);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('faculty'));
        $response = $response['faculties'];
        $this->assertEquals("AppSci", $response[0]['name']);
    }

    /**
     * @depends testUpdateAction
     */
    public function testCreateAction()
    {
        $response = $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('faculty_create'),'{"name":"Music"}');
        $this->assertEquals("Music", $response['name']);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('faculty'));
        $this->assertCount(4 + 1, $response['faculties']);
    }

    /**
     * @depends testCreateAction
     */
    public function testDeleteAction()
    {
        $this->getAndTestJSONResponseFrom("DELETE",
            $this->getUrl('faculty_delete', array('id'=> 5)),'', 204);
        $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('faculty_show', array('id'=> 5)),'', 404);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('faculty'));
        $this->assertCount(4, $response['faculties']);
    }

    /**
     * @depends testDeleteAction
     */
    public function testAddUser()
    {
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('faculty_user_add', array('id' => 2, 'user' => '1')), '', 204);
        $response = $this->getAndTestJSONResponseFrom('GET', $this->getUrl('user_show', array('id' => 1)));
        $this->assertEquals('Science', $response['faculties'][0]['name']);
    }

    /**
     * @depends testAddUser
     */
    public function testRemoveUser()
    {
        $this->getAndTestJSONResponseFrom("DELETE", $this->getUrl('faculty_user_delete', array('id' => 2, 'user' => '1')), '', 204);
        $response = $this->getAndTestJSONResponseFrom('GET', $this->getUrl('user_show', array('id' => 1)));
        $this->assertCount(0, $response['faculties']);
    }

    /*
     * =============================================
     * Invalid Action Tests
     * =============================================
     */

    public function testShowActionInvalid()
    {
        $this->loadFixtures($this->IpeerFixtures);
        $this->getAndTestJSONResponseFrom("GET", $this->getUrl('faculty_show', array('id' => LoadFacultyData::NUMBER_OF_FACULTIES * 2)), '', 404);
    }

    /**
     * @depends testShowActionInvalid
     */
    public function testCreateActionInvalid()
    {
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('faculty_create'), '', 400);
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('faculty_create'), '{}', 400);
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('faculty_create'), '{"faculty_name":""}', 400);
    }

    /**
     * @depends testShowActionInvalid
     */
    public function testUpdateActionInvalid()
    {
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('faculty_update', array('id'=>1)), '{}', 400);
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('faculty_update', array('id'=> LoadFacultyData::NUMBER_OF_FACULTIES * 2)), '{"name":"Science"}', 404);
    }

    /**
     * @depends testUpdateActionInvalid
     */
    public function testAddUserInvalid()
    {
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('faculty_user_add', array('id'=>1, 'user'=> LoadUserData::NUMBER_OF_USERS * 2)), '', 404);
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('faculty_user_add', array('id'=>LoadFacultyData::NUMBER_OF_FACULTIES, 'user'=> 1)), '', 404);
    }

    /**
     * @depends testAddUserInvalid
     */
    public function testRemoveUserInvalid()
    {
        $this->getAndTestJSONResponseFrom("DELETE", $this->getUrl('faculty_user_delete', array('id'=>1, 'user'=> 1)), '', 404);
        $this->getAndTestJSONResponseFrom("DELETE", $this->getUrl('faculty_user_delete', array('id'=>1, 'user'=> LoadUserData::NUMBER_OF_USERS * 2)), '', 404);
        $this->getAndTestJSONResponseFrom("DELETE", $this->getUrl('faculty_user_delete', array('id'=>LoadFacultyData::NUMBER_OF_FACULTIES, 'user'=> 1)), '', 404);
    }
}
