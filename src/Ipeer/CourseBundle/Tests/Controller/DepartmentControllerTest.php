<?php

namespace Ipeer\CourseBundle\Tests\Controller;

use Ipeer\ApiUtilityBundle\Test\IpeerTestCase;
use Ipeer\CourseBundle\DataFixtures\ORM\LoadCourseData;
use Ipeer\CourseBundle\DataFixtures\ORM\LoadFacultyData;

class DepartmentControllerTest extends IpeerTestCase
{
    /*
     * =============================================
     * Valid Action Tests
     * =============================================
     */

    public function testIndexAction()
    {
        $this->loadFixtures($this->IpeerFixtures);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('department', array('faculty' => 2)));
        $this->assertCount(2, $response['departments']);
        $this->assertEquals("CPSC", $response['departments'][0]['name']);
        $this->assertEquals("MATH", $response['departments'][1]['name']);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('department', array('faculty' => 1)));
        $this->assertCount(1, $response['departments']);
        $this->assertEquals("MECH", $response['departments'][0]['name']);
    }

    /**
     * @depends testIndexAction
     */
    public function testShowAction()
    {
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('department_show', array('faculty' => 2, 'id' => 2)));
        $this->assertEquals("CPSC", $response['name']);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('department_show', array('faculty' => 1, 'id' => 1)));
        $this->assertEquals("MECH", $response['name']);
    }

    /**
     * @depends testShowAction
     */
    public function testCreateAction()
    {
        $response = $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('department_create', array('faculty' => 2)), '{"name":"EOSC"}');
        $this->assertEquals("EOSC", $response['name']);
        $courseIdEOSC = $response['id'];
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('department', array('faculty' => 2)));
        $this->assertCount(2 + 1, $response['departments']);
        return $courseIdEOSC; //gets passed to testDeleteAction
    }

    /**
     * @param integer $courseIdEOSC
     *
     * @depends testCreateAction
     */
    public function testDeleteAction($courseIdEOSC)
    {
        $this->getAndTestJSONResponseFrom("DELETE",
            $this->getUrl('department_delete', array('faculty' => 2, 'id' => $courseIdEOSC)), '', 204);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('department', array('faculty' => 2)));
        $this->assertCount(2, $response['departments']);
    }

    /**
     * @depends testDeleteAction
     */
    public function testAddCourse()
    {
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('department_course_add', array('faculty' => 2, 'id' => 2, 'course' => 1)), '', 204);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('department_show', array('faculty' => 2, 'id' => 2)));
        $this->assertCount(1 + 1, $response['courses']);
    }

    /**
     * @depends testAddCourse
     */
    public function testRemoveCourse()
    {
        $this->getAndTestJSONResponseFrom("DELETE",
            $this->getUrl('department_course_delete', array('faculty' => 2, 'id' => 2, 'course' => 1)), '', 204);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('department_show', array('faculty' => 2, 'id' => 2)));
        $this->assertCount(1, $response['courses']);
    }

    /**
     * @depends testRemoveCourse
     */
    public function testUpdateAction()
    {
        $response = $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('department_update', array('faculty' => 2, 'id' => 2)), '{"name": "Computer Science"}');
        $this->assertEquals("Computer Science", $response['name']);
    }

    /*
     * =============================================
     * Invalid Action Tests
     * =============================================
     */

    public function testShowActionInvalid()
    {
        $this->loadFixtures($this->IpeerFixtures);
        $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('department_show', array('faculty' => 2, 'id' => 1)), '', 404);
        $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('department_show', array('faculty' => LoadFacultyData::NUMBER_OF_FACULTIES * 2, 'id' => 1)), '', 404);
        $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('department_show', array('faculty' => 1, 'id' => 2)), '', 404);
    }

    /**
     * @depends testShowActionInvalid
     */
    public function testCreateActionInvalid()
    {
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('department_create', array('faculty' => 2)), '', 400);
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('department_create', array('faculty' => 2)), '{}', 400);
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('department_create', array('faculty' => LoadFacultyData::NUMBER_OF_FACULTIES * 2)), '{"name":"EOSC"}', 404);
    }

    /**
     * @depends testShowActionInvalid
     */
    public function testUpdateActionInvalid()
    {
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('department_update', array('faculty' => 2, 'id'=> 2)), '{}', 400);
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('department_update', array('faculty' => 2, 'id' => 1)), '{"name":"EOSC"}', 404);
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('department_update', array('faculty' => LoadFacultyData::NUMBER_OF_FACULTIES * 2, 'id'=>2)), '{"name":"EOSC"}', 404);
    }

    /**
     * @depends testUpdateActionInvalid
     */
    public function testAddCourseInvalid()
    {
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('department_course_add', array('faculty' => 2, 'id'=> 2, 'course' => LoadCourseData::NUMBER_OF_COURSES * 2)), '', 404);
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('department_course_add', array('faculty' => 2, 'id'=> 100, 'course' => 1)), '', 404);
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('department_course_add', array('faculty' => LoadFacultyData::NUMBER_OF_FACULTIES, 'id'=> 2, 'course' => 1)), '', 404);
    }

    /**
     * @depends testAddCourseInvalid
     */
    public function testRemoveCourseInvalid()
    {
        $this->getAndTestJSONResponseFrom("DELETE",
            $this->getUrl('department_course_delete', array('faculty' => 2, 'id'=> 2, 'course' => 1)), '', 404);
    }
}
