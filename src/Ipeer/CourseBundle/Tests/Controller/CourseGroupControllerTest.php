<?php

namespace Ipeer\CourseBundle\Tests\Controller;

use Ipeer\ApiUtilityBundle\Test\IpeerTestCase;
use Ipeer\CourseBundle\DataFixtures\ORM\LoadCourseData;
use Ipeer\CourseBundle\DataFixtures\ORM\LoadGroupData;

class CourseGroupControllerTest extends IpeerTestCase
{
    /*
     * =============================================
     * Valid Action Tests
     * =============================================
     */

    public function testIndexActionNoGroups()
    {
        $this->loadFixtures($this->IpeerFixtures);
        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group', array('course' => 4)));
        $this->assertCount(0, $response['groups']);
    }

    /**
     * @depends testIndexActionNoGroups
     */
    public function testIndexActionNoMembers()
    {
        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group', array('course' => 4)));
        $this->assertCount(0, $response['groups']);
    }

    /**
     * @depends testIndexActionNoGroups
     */
    public function testShowGroupAction()
    {
        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group', array('course' => 1)));
        $response = $response['groups'];
        $this->assertCount(2, $response);
        $this->assertEquals($response[0]['name'], 'APSC201-Group01');
        $this->assertEquals($response[1]['name'], 'APSC201-Group02');
    }

    /**
     * @depends testShowGroupAction
     */
    public function testShowMembersAction()
    {
        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group_show', array('course' => 1, 'id' => 1)));
        $this->assertEquals($response['group']['name'], 'APSC201-Group01');
        $response = $response['members'];

        $this->assertCount(4, $response);

        $expectedStudents = array('18', '19', '20');

        foreach($response as $user) {
            if($response[0]['course_role'] == 0) {
                // unset -> "mark" a student as found
                // if a non-expected student is found, the expression will generate an error, failing the test
                unset($expectedStudents[array_search($user['user']['id'], $expectedStudents)]);
            } else {
                // assume $response[0]['course_role'] == 1
                $this->assertEquals($user['user']['id'], 14);
            }
        }

        $this->assertCount(0, $expectedStudents); // we got all the expected students
    }

    /**
     * @depends testShowMembersAction
     */
    public function testUpdateGroupAction()
    {
        $response = $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_update', array('course' => 1, 'id' => 1)),
            '{"name": "APSC201-NewGroup01"}');
        $this->assertEquals('APSC201-NewGroup01', $response['group']['name']);
        $this->assertEquals('1', $response['group']['id']);
        $this->assertCount(4, $response['members']);
    }

    /**
     * @depends testUpdateGroupAction
     */
    public function testAddMemberAction()
    {
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_member_add', array('course' => 1, 'id' => 1, 'user' => 21)), '', 204);

        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group_show', array('course' => 1, 'id' => 1)));
        $this->assertCount(4+1, $response['members']);
    }

    /**
     * @depends testAddMemberAction
     */
    public function testDeleteMemberAction()
    {
        $this->getAndTestJSONResponseFrom("DELETE", $this->getUrl('group_member_delete', array('course' => 1, 'id' => 1, 'user' => 21)), '', 204);

        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group_show', array('course' => 1, 'id' => 1)));
        $this->assertCount(4+1-1, $response['members']);
    }

    /**
     * @depends testUpdateGroupAction
     */
    public function testCreateGroupAction()
    {
        $response = $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_create', array('course' => 1)), '{"name" : "APSC201-newgroup"}');
        $this->assertEquals($response['group']['name'], "APSC201-newgroup");
        $this->assertCount(0, $response['members']);
        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group', array('course' => 1)));
        $response = $response['groups'];
        $this->assertCount(2 + 1, $response);
        $this->assertEquals($response[2]['name'], 'APSC201-newgroup');

        return $response[2]['id'];
    }

    /**
     * @param integer
     *
     * @depends testCreateGroupAction
     */
    public function testDeleteGroupAction($idOfNewGroup)
    {
        $this->getAndTestJSONResponseFrom("DELETE", $this->getUrl('group_delete', array('course' => 1, 'id' => $idOfNewGroup)), '', 204);
        $response = $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group', array('course' => 1)));
        $response = $response['groups'];
        $this->assertCount(2 + 1 - 1, $response);
    }

    /*
     * =============================================
     * Invalid Action Tests
     * =============================================
     */

    public function testShowGroupsActionInvalid()
    {
        $this->loadFixtures($this->IpeerFixtures);

        // bad course id
        $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group', array('course' => LoadCourseData::NUMBER_OF_COURSES * 2)), '', 404);
    }

    /**
     * @depends testShowGroupsActionInvalid
     */
    public function testShowMembersActionInvalid()
    {
        // bad course id
        $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group_show', array('course' => LoadCourseData::NUMBER_OF_COURSES * 2, 'id' => 1)), '', 404);
        // mismatched group id (not in that course)
        $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group_show', array('course' => 1, 'id' => 3)), '', 404);
        // bad group id (does not exist at all)
        $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group_show', array('course' => 1, 'id' => LoadGroupData::NUMBER_OF_GROUPS * 2)), '', 404);
        // bad course id and bad group id (does not exist at all)
        $this->getAndTestJSONResponseFrom("GET", $this->getUrl('group_show', array('course' => LoadCourseData::NUMBER_OF_COURSES, 'id' => LoadGroupData::NUMBER_OF_GROUPS * 2)), '', 404);
    }

    /**
     * @depends testShowMembersActionInvalid
     */
    public function testUpdateGroupActionInvalid()
    {
        // course not found
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_update', array('course' => LoadCourseData::NUMBER_OF_COURSES * 2, 'id' => 1)), '', 404);
        // group not found
        // (however it 400s because deserialization fails first
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_update', array('course' => 1, 'id' => 4)), '', 400);
        // missing fields
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_update', array('course' => 1, 'id' => 1)), '', 400);
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_update', array('course' => 1, 'id' => 1)), '{}', 400);
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_update', array('course' => 1, 'id' => 1)), '{"namegroup":""}', 400);
        // not found and missing fields
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_update', array('course' => 1, 'id' => 4)), '{"namegroup":""}', 400);
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_update', array('course' => 1, 'id' => 4)), '{"name":""}', 400);
    }

    public function testAddMemberActionInvalid()
    {
        // bad course
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_member_add', array('course' => LoadCourseData::NUMBER_OF_COURSES, 'id' => 1, 'user' => 21)), '', 404);
        // bad group
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_member_add', array('course' => 1, 'id' => LoadGroupData::NUMBER_OF_GROUPS, 'user' => 21)), '', 404);
        // user not enrolled
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_member_add', array('course' => 1, 'id' => 1, 'user' => 25)), '', 400);
        // instructor (can't be put into group)
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_member_add', array('course' => 1, 'id' => 1, 'user' => 7)), '', 400);
    }

    public function testCreateGroupActionInvalid()
    {
        // bad course
        // (however it 400s because deserialization fails first
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_create', array('course' => LoadCourseData::NUMBER_OF_COURSES)), '', 400);
        // missing data
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_create', array('course' => 1)), '', 400);
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_create', array('course' => 1)), '{}', 400);
        $this->getAndTestJSONResponseFrom("POST", $this->getUrl('group_create', array('course' => 1)), '{"name":""}', 400);
    }

    public function testDeleteMemberActionInvalid()
    {
        // bad course
        $this->getAndTestJSONResponseFrom("DELETE", $this->getUrl('group_member_delete', array('course' => LoadCourseData::NUMBER_OF_COURSES, 'id' => 1, 'user' => 18)), '', 404);
        // bad group
        $this->getAndTestJSONResponseFrom("DELETE", $this->getUrl('group_member_delete', array('course' => 1, 'id' => LoadGroupData::NUMBER_OF_GROUPS * 2, 'user' => 18)), '', 404);
        // never a member in the first place
        $this->getAndTestJSONResponseFrom("DELETE", $this->getUrl('group_member_delete', array('course' => 1, 'id' => 1, 'user' => 21)), '', 404);
    }

    public function testDeleteGroupActionInvalid()
    {
        // mismatch course with group
        $this->getAndTestJSONResponseFrom("DELETE", $this->getUrl('group_delete', array('course' => 2, 'id' => 1)), '', 404);
        // group doesn't exist
        $this->getAndTestJSONResponseFrom("DELETE", $this->getUrl('group_delete', array('course' => 1, 'id' => LoadGroupData::NUMBER_OF_GROUPS)), '', 404);
    }

}
