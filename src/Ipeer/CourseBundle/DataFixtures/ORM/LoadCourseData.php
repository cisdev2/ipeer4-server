<?php

namespace Ipeer\CourseBundle\DataFixtures\ORM;

use Ipeer\ApiUtilityBundle\DataFixtures\ORM\SingletonDataFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Ipeer\CourseBundle\Entity\Course;

class LoadCourseData extends SingletonDataFixture implements OrderedFixtureInterface {

    /**
     * {@inheritdoc}
     */
    protected function makeData()
    {

        // enrollments and groups are done in other fixtures
        $coursesData = array(
            array("APSC201"),
            array("MECH220"),
            array("CPSC312"),
            array("MATH342"),
            array("ENGL112"),
            array("ARTS001"),
            array("COMM335"),
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
