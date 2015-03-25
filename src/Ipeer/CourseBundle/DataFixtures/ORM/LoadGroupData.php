<?php

namespace Ipeer\CourseBundle\DataFixtures\ORM;

use Ipeer\ApiUtilityBundle\DataFixtures\ORM\DataLoadingFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Ipeer\CourseBundle\Entity\CourseGroup;
use Ipeer\CourseBundle\Entity\Course;

class LoadGroupData extends DataLoadingFixture implements OrderedFixtureInterface {

    const NUMBER_OF_GROUPS = 7;

    /**
     * {@inheritdoc}
     */
    protected function makeData()
    {
        $groupsData = array(
            array("APSC201", "Group01", // id = 1; index = 0
                array("student01@ipeer.ubc",
                    "student02@ipeer.ubc",
                    "student03@ipeer.ubc",
                    "tutor01@ipeer.ubc")),
            array("APSC201", "Group02",
                array("student04@ipeer.ubc",
                    "student05@ipeer.ubc",
                    "student06@ipeer.ubc")),
            array("MECH220", "Group01", // id = 3; index = 2
                array("student07@ipeer.ubc",
                    "student08@ipeer.ubc",
                    "student09@ipeer.ubc",
                    "tutor02@ipeer.ubc")),
            array("MECH220", "Group02",
                array("student10@ipeer.ubc",
                    "student11@ipeer.ubc",
                    "student12@ipeer.ubc")),
            array("MECH220", "Group03", // id = 5; index = 4
                array("student13@ipeer.ubc",
                    "student14@ipeer.ubc",
                    "student15@ipeer.ubc")),
            array("CPSC312", "Group01",
                array("student16@ipeer.ubc",
                    "student17@ipeer.ubc",
                    "student18@ipeer.ubc")),
            array("CPSC312", "Empty02", // id = 7; index = 6
                array()),
        );

        $groups = array();

        foreach($groupsData as $groupData) {
            $group = new CourseGroup();

            $course = $this->getReference('course-'.$groupData[0]);
            $course->addCourseGroup($group);

            $group->setName($groupData[0] . '-' .$groupData[1]);
            foreach($groupData[2] as $email) {
                $group->addEnrollment($this->getReference('enrol-' . $groupData[0] . '-' . $email));
            }
            $groups[] = $group;
        }

        return $groups;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 30;
    }

}
