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
            array("APSC201", null), // id = 1, index = 0
            array("MECH220", "MECH"),
            array("CPSC312", "CPSC"), // id = 3, index = 2
            array("MATH342", "MATH"),
            array("ENGL112", "ENGL"), // id = 5, index = 4
            array("ARTS001", null),
            array("COMM335", "COMM"), // id = 7, index = 6
            array("TEST000", null) // empty course
        );

        $courses = array();

        foreach($coursesData as $courseData) {
            $course = new Course();
            $course->setName($courseData[0]);
            if(null !== $courseData[1]) {
                $this->getReference('department-' . $courseData[1])->addCourse($course);
            }
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
