<?php

namespace Ipeer\CourseBundle\DataFixtures\ORM;

use Ipeer\ApiUtilityBundle\DataFixtures\ORM\DataLoadingFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Ipeer\CourseBundle\Entity\CourseGroup;

class LoadGroupData extends DataLoadingFixture implements OrderedFixtureInterface {

    /**
     * {@inheritdoc}
     */
    protected function makeData()
    {
        $groupsData = array(
            array("APSC201", "Group01",
                array("student01@ipeer.ubc",
                    "student02@ipeer.ubc",
                    "student03@ipeer.ubc")),
            array("APSC201", "Group02",
                array("student01@ipeer.ubc",
                    "student02@ipeer.ubc",
                    "student03@ipeer.ubc")),
        );

        $groups = array();

        foreach($groupsData as $groupData) {
            $group = new CourseGroup();
            $group->setCourse($this->getReference('course-'.$groupData[0]));
            $group->setName($groupData[1]);
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
