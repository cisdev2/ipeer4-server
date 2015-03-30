<?php

namespace Ipeer\CourseBundle\Tests\Controller;

use Ipeer\ApiUtilityBundle\Test\IpeerTestCase;
use Ipeer\CourseBundle\DataFixtures\ORM\LoadCourseData;

class CourseControllerTest extends IpeerTestCase {

    /*
     * =============================================
     * Helper functions
     * =============================================
     */

    private function assertCourseEquals($courseExpected, $courseActual)
    {
        $this->assertEquals($courseExpected[0], $courseActual['name']);
    }

    /*
     * =============================================
     * Valid Action Tests
     * =============================================
     */

    public function testIndexActionEmpty()
    {
        $this->loadFixtures(array());
        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('course'));
        $this->assertCount(0, $response["courses"]);
    }

    /**
     * @depends testIndexActionEmpty
     */
    public function testIndexAction()
    {
        $this->loadFixtures($this->IpeerFixtures);

        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('course'));
        $response = $response["courses"];

        $this->assertCount(LoadCourseData::NUMBER_OF_COURSES, $response);

        $this->assertCourseEquals(array("APSC201"), $response[0]);
        $this->assertCourseEquals(array("CPSC312"), $response[2]);
        $this->assertCourseEquals(array("ARTS001"), $response[5]);
    }

    /**
     * @depends testIndexAction
     */
    public function testShowAction()
    {
        $this->assertCourseEquals(array("APSC201"),
            $this->getAndTestJSONResponseFrom("GET", $this->getUrl('course_show', array('id' => 1))));
        $this->assertCourseEquals(array("MATH342"),
            $this->getAndTestJSONResponseFrom("GET", $this->getUrl('course_show', array('id' => 4))));
        $this->assertCourseEquals(array("COMM335"),
            $this->getAndTestJSONResponseFrom("GET", $this->getUrl('course_show', array('id' => 7))));
    }

    /**
     * @depends testShowAction
     */
    public function testUpdateAction()
    {
        $route =  $this->getUrl('course_update', array('id' => 1));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"name" : "Course1"}');
        $route =  $this->getUrl('course_update', array('id' => 2));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"name" : "Course2"}');
        $route =  $this->getUrl('course_update', array('id' => 3));
        $this->getAndTestJSONResponseFrom('POST', $route,
            '{"name" : "Course3"}');

        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('course'));
        $response = $response["courses"];
        $this->assertCount(LoadCourseData::NUMBER_OF_COURSES, $response);

        $this->assertCourseEquals(array("Course1"), $response[1-1]);
        $this->assertCourseEquals(array("Course2"), $response[2-1]);
        $this->assertCourseEquals(array("Course3"), $response[3-1]);
    }

    /**
     * @depends testUpdateAction
     */
    public function testCreateAction()
    {
        $route = $this->getUrl('course');

        $data = $this->getAndTestJSONResponseFrom("POST", $route,
            '{"name": "NewCourseA"}');
        $this->assertCourseEquals(array("NewCourseA"), $data);
        $this->assertCourseEquals(1 + LoadCourseData::NUMBER_OF_COURSES, $data['id']);

        $data = $this->getAndTestJSONResponseFrom("POST", $route,
            '{"name": "NewCourseB"}');
        $this->assertCourseEquals(array("NewCourseB"), $data);
        $this->assertCourseEquals(2 + LoadCourseData::NUMBER_OF_COURSES, $data['id']);

        $route = $this->getUrl('course');
        $response = $this->getAndTestJSONResponseFrom("GET", $route);
        $this->assertCount(2 + LoadCourseData::NUMBER_OF_COURSES, $response["courses"]);
    }

    /*
     * No dependencies.
     */
    public function testDeleteAction()
    {
        $this->loadFixtures($this->IpeerFixtures);

        $route =  $this->getUrl('course_delete', array('id' => 1));
        $this->getAndTestJSONResponseFrom('DELETE', $route, '', 204);

        $route =  $this->getUrl('course_delete', array('id' => 3));
        $this->getAndTestJSONResponseFrom('DELETE', $route, '', 204);

        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('course'));
        $this->assertCount(LoadCourseData::NUMBER_OF_COURSES-2, $response["courses"]); //removed 2 users

        // check that the users truly no longer exist
        $route =  $this->getUrl('course_show', array('id' => 1));
        $this->getAndTestJSONResponseFrom("GET", $route, '', 404);

        $route =  $this->getUrl('course_show', array('id' => 3));
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

        $route =  $this->getUrl('course_show', array('id' => 0));
        $this->getAndTestJSONResponseFrom("GET", $route, '', 404);

        $route =  $this->getUrl('course_show', array('id' => LoadCourseData::NUMBER_OF_COURSES * 2));
        $this->getAndTestJSONResponseFrom("GET", $route, '', 404);
    }

    /**
     * @depends testShowActionInvalid
     */
    public function testCreateActionInvalid()
    {
        $route = $this->getUrl('course');

        // various corruptions of data (blank, empty object, missing various fields)
        $this->getAndTestJSONResponseFrom("POST", $route,
            '', 400);
        $this->getAndTestJSONResponseFrom("POST", $route,
            '{}', 400);
        $this->getAndTestJSONResponseFrom("POST", $route,
            '{"name" : ""}', 400);

        $response = $this->getAndTestJSONResponseFrom("GET", $route);
        $this->assertCount(LoadCourseData::NUMBER_OF_COURSES, $response["courses"]); // no courses created
    }

    /**
     * @depends testCreateActionInvalid
     */
    public function testUpdateActionInvalid()
    {
        $route = $this->getUrl('course_update', array('id' => 1));

        // various corruptions of data (blank, empty object, missing various fields)
        $this->getAndTestJSONResponseFrom("POST", $route,
            '', 400);
        $this->getAndTestJSONResponseFrom("POST", $route,
            '{}', 400);
        $this->getAndTestJSONResponseFrom("POST", $route,
            '{"name" : ""}', 400);

        // valid and invalid updates to a non-existent entity should 404
        $route = $this->getUrl('course_update', array('id' => LoadCourseData::NUMBER_OF_COURSES * 2));
        $this->getAndTestJSONResponseFrom("POST", $route,
            '{"name" : ""}', 404);
        $this->getAndTestJSONResponseFrom("POST", $route,
            '{"name" : "validName"}', 404);

        $route = $this->getUrl('course');
        $response = $this->getAndTestJSONResponseFrom("GET", $route);
        $this->assertCount(LoadCourseData::NUMBER_OF_COURSES, $response["courses"]); // no courses created

    }

    /**
     * @depends testUpdateActionInvalid
     */
    public function testDeleteActionInvalid()
    {
        $route =  $this->getUrl('course_delete', array('id' => LoadCourseData::NUMBER_OF_COURSES * 2 ));
        $this->getAndTestJSONResponseFrom('DELETE', $route, '', 404);

        $route =  $this->getUrl('course_delete', array('id' => 0));
        $this->getAndTestJSONResponseFrom('DELETE', $route, '', 404);
    }
}