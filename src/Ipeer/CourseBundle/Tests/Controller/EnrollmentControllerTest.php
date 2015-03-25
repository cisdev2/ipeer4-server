<?php

namespace Ipeer\CourseBundle\Tests\Controller;

use Ipeer\ApiUtilityBundle\Test\JSONTestCase;
use Ipeer\CourseBundle\DataFixtures\ORM\LoadCourseData;
use Ipeer\CourseBundle\Entity\Enrollment;
use Ipeer\UserBundle\DataFixtures\ORM\LoadUserData;

class EnrollmentControllerTest extends JSONTestCase
{

    /*
     * =============================================
     * Fixtures to load and helper functions
     * =============================================
     */

    private $standardSampleData = array(
        'Ipeer\UserBundle\DataFixtures\ORM\LoadUserData',
        'Ipeer\CourseBundle\DataFixtures\ORM\LoadCourseData',
        'Ipeer\CourseBundle\DataFixtures\ORM\LoadEnrollmentData',
        'Ipeer\CourseBundle\DataFixtures\ORM\LoadGroupData',
    );

    /**
     * @param array $data
     * @param integer $numInstructors
     * @param integer $numTutors
     * @param integer $numStudents
     */
    private function verifyEnrollmentCounts($data, $numInstructors, $numTutors, $numStudents) {
        $countInstructors = 0;
        $countStudents = 0;
        $countTutors = 0;

        $this->assertCount($numInstructors+$numTutors+$numStudents, $data,
            "Wrong number of enrollments in course");

        foreach($data as $enrol) {
            switch($enrol['course_role']) {
                case Enrollment::STUDENT_ROLE:
                    $countStudents += 1;
                    break;
                case Enrollment::INSTRUCTOR_ROLE:
                    $countInstructors += 1;
                    break;
                case Enrollment::TUTOR_ROLE:
                    $countTutors += 1;
                    break;
            }
        }

        $this->assertEquals($countInstructors, $numInstructors, "Wrong number of instructors");
        $this->assertEquals($countTutors, $numTutors, "Wrong number of tutors");
        $this->assertEquals($countStudents, $numStudents, "Wrong number of students");
    }

    /*
     * =============================================
     * Valid Action Tests
     * ============================================
     */

    public function testIndexActionEmpty() {
        $this->loadFixtures($this->standardSampleData);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 8)));
        $this->verifyEnrollmentCounts($response["enrollments"], 0,0,0);
    }

    /**
     * @depends testIndexActionEmpty
     */
    public function testIndexAction() {
        // apsc 201
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 1)));
        $this->verifyEnrollmentCounts($response["enrollments"], 3, 1, 6);

        // engl 112
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 5)));
        $this->verifyEnrollmentCounts($response["enrollments"], 2, 0, 4);

        // cpsc 312
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 3)));
        $this->verifyEnrollmentCounts($response["enrollments"], 4, 2, 4);
    }

    /**
     * @depends testIndexAction
     */
    public function testUpdateAction() {
        // student to tutor (? is this needed)
        // should remain in groups
        // apsc 201 (1), group01 (1), student01 (18)
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('enrollment_update', array('course' => 1, 'user' => 18)),
                '{"course_role" : "1"}', 204);
        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group_show', array('course' => 1, 'id' => 1)));
        $this->assertCount(4, $response['members']);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 1)));
        $this->verifyEnrollmentCounts($response["enrollments"], 3, 2, 5);

        // student to instructor (? is this needed)
        // should get kicked out of groups
        // apsc 201 (1), group02 (2), student04 (21)
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('enrollment_update', array('course' => 1, 'user' => 21)),
            '{"course_role" : "2"}', 204);
        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group_show', array('course' => 1, 'id' => 2)));
        $this->assertCount(2, $response['members']);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 1)));
        $this->verifyEnrollmentCounts($response["enrollments"], 4, 2, 4);

        // student to student (no change)
        // should remain in groups
        // apsc 201 (1), group01 (1), student02 (19)
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('enrollment_update', array('course' => 1, 'user' => 19)),
            '{"course_role" : "0"}', 204);
        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group_show', array('course' => 1, 'id' => 1)));
        $this->assertCount(4, $response['members']);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 1)));
        $this->verifyEnrollmentCounts($response["enrollments"], 4, 2, 4);

        // tutor to student (? is this needed)
        // should remain in groups
        // apsc 201 (1), group01 (1), student01 (18)
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('enrollment_update', array('course' => 1, 'user' => 18)),
            '{"course_role" : "0"}', 204);
        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group_show', array('course' => 1, 'id' => 1)));
        $this->assertCount(4, $response['members']);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 1)));
        $this->verifyEnrollmentCounts($response["enrollments"], 4, 1, 5);

        // tutor to instructor
        // should get kicked out of groups
        // apsc 201 (1), group01 (1), tutor01 (14)
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('enrollment_update', array('course' => 1, 'user' => 14)),
            '{"course_role" : "2"}', 204);
        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group_show', array('course' => 1, 'id' => 1)));
        $this->assertCount(3, $response['members']);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 1)));
        $this->verifyEnrollmentCounts($response["enrollments"], 5, 0, 5);

        // tutor to tutor (no change)
        // should remain in groups
        // mech 220 (2), group01 (3), tutor02 (15)
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('enrollment_update', array('course' => 2, 'user' => 15)),
            '{"course_role" : "1"}', 204);
        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group_show', array('course' => 2, 'id' => 3)));
        $this->assertCount(4, $response['members']);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 2)));
        $this->verifyEnrollmentCounts($response["enrollments"], 2, 4, 9);

        // instructor to tutor
        // groups irrelevant
        // mech 220 (2), mechInstr (8)
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('enrollment_update', array('course' => 2, 'user' => 8)),
            '{"course_role" : "1"}', 204);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 2)));
        $this->verifyEnrollmentCounts($response["enrollments"], 1, 5, 9);

        // instructor to instructor (no change)
        // groups irrelevant
        // mech 220 (2), apscInstr (7)
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('enrollment_update', array('course' => 2, 'user' => 7)),
            '{"course_role" : "2"}', 204);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 2)));
        $this->verifyEnrollmentCounts($response["enrollments"], 1, 5, 9);

        // instructor to student (? is this needed)
        // groups irrelevant
        // mech 220 (2), apscInstr (7)
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('enrollment_update', array('course' => 2, 'user' => 7)),
            '{"course_role" : "0"}', 204);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 2)));
        $this->verifyEnrollmentCounts($response["enrollments"], 0, 5, 10);
    }

    /**
     * @depends testUpdateAction
     */
    public function testCreateAction() {
        // create student
        // empty course (id 8)

        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('enrollment_update', array('course' => 8, 'user' => 7)),
            '{"course_role" : "0"}', 204);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 8)));
        $this->verifyEnrollmentCounts($response["enrollments"], 0, 0, 1);

        // create instructor
        // empty course (id 8)
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('enrollment_update', array('course' => 8, 'user' => 6)),
            '{"course_role" : "2"}', 204);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 8)));
        $this->verifyEnrollmentCounts($response["enrollments"], 1, 0, 1);

        // create tutor
        // empty course (id 8)
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('enrollment_update', array('course' => 8, 'user' => 5)),
            '{"course_role" : "1"}', 204);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 8)));
        $this->verifyEnrollmentCounts($response["enrollments"], 1, 1, 1);
    }

    /**
     * @depends testCreateAction
     */
    public function testDeleteAction() {
        // delete student
        // empty course (id 8)

        $this->getAndTestJSONResponseFrom("DELETE",
            $this->getUrl('enrollment_update', array('course' => 8, 'user' => 7)),
            '', 204);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 8)));
        $this->verifyEnrollmentCounts($response["enrollments"], 1, 1, 0);

        // delete instructor
        // empty course (id 8)
        $this->getAndTestJSONResponseFrom("DELETE",
            $this->getUrl('enrollment_update', array('course' => 8, 'user' => 6)),
            '', 204);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 8)));
        $this->verifyEnrollmentCounts($response["enrollments"], 0, 1, 0);

        // delete tutor
        // empty course (id 8)
        $this->getAndTestJSONResponseFrom("DELETE",
            $this->getUrl('enrollment_update', array('course' => 8, 'user' => 5)),
            '', 204);
        $response = $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => 8)));
        $this->verifyEnrollmentCounts($response["enrollments"], 0, 0, 0);
    }

    /*
     * =============================================
     * Invalid Action Tests
     * ============================================
     */

    public function testCreateActionInvalid() {
        $this->loadFixtures($this->standardSampleData);

        // non existent course
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('enrollment_update', array('course' => LoadCourseData::NUMBER_OF_COURSES * 2, 'user' => 5)),
            '{"course_role" : "1"}', 404);

        // non existent user
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('enrollment_update', array('course' => 1, 'user' => LoadUserData::NUMBER_OF_USERS)),
            '{"course_role" : "1"}', 404);

        // blank request
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('enrollment_update', array('course' => 8, 'user' => 7)),
            '', 400);

        // empty object
        $this->getAndTestJSONResponseFrom("POST",
            $this->getUrl('enrollment_update', array('course' => 8, 'user' => 7)),
            '{}', 400);

    }

    /**
     * @depends testCreateActionInvalid
     */
    public function testShowActionInvalid() {
        $this->getAndTestJSONResponseFrom("GET",
            $this->getUrl('enrollment', array('course' => LoadCourseData::NUMBER_OF_COURSES * 2)),
            '', 404);
    }

    /**
     * @depends testShowActionInvalid
     */
    public function testDeleteActionInvalid() {
        // non existent course
        $this->getAndTestJSONResponseFrom("DELETE",
            $this->getUrl('enrollment_update', array('course' => LoadCourseData::NUMBER_OF_COURSES * 2, 'user' => 5)),
            '', 404);

        // non existent user
        $this->getAndTestJSONResponseFrom("DELETE",
            $this->getUrl('enrollment_update', array('course' => 1, 'user' => LoadUserData::NUMBER_OF_USERS)),
            '', 404);

        // non existent enrollment
        // student16 (id 33) is not in mech 220 (id 2)
        $this->getAndTestJSONResponseFrom("DELETE",
            $this->getUrl('enrollment_update', array('course' => 2, 'user' => 33)),
            '', 404);
    }
}
