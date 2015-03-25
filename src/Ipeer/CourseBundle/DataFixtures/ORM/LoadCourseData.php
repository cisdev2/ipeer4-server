<?php

namespace Ipeer\CourseBundle\DataFixtures\ORM;

use Ipeer\ApiUtilityBundle\DataFixtures\ORM\DataLoadingFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Ipeer\CourseBundle\Entity\Course;

class LoadCourseData extends DataLoadingFixture implements OrderedFixtureInterface {

    const NUMBER_OF_COURSES = 8;

    /**
     * {@inheritdoc}
     */
    protected function makeData()
    {

        // enrollments and groups are done in other fixtures
        $coursesData = array(
            array("APSC201"), // id = 1, index = 0
            array("MECH220"),
            array("CPSC312"), // id = 3, index = 2
            array("MATH342"),
            array("ENGL112"), // id = 5, index = 4
            array("ARTS001"),
            array("COMM335"), // id = 7, index = 6
            array("TEST000") // empty course
        );

        $courses = array();

        foreach($coursesData as $courseData) {
            $course = new Course();
            $course->setName($courseData[0]);
            $this->setReference('course-' . $courseData[0], $course);

            $courses[] = $course;
        }

        return $courses;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 10;
    }
}
