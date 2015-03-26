<?php

namespace Ipeer\CourseBundle\DataFixtures\ORM;

use Ipeer\ApiUtilityBundle\DataFixtures\ORM\DataLoadingFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Ipeer\CourseBundle\Entity\Faculty;

class LoadFacultyData extends DataLoadingFixture implements OrderedFixtureInterface {

    const NUMBER_OF_FACULTIES = 4;

    /**
     * {@inheritdoc}
     */
    protected function makeData()
    {
        $facultiesData = array(
            array("Applied Science"),
            array("Science"),
            array("Business"),
            array("Arts")
        );

        $faculties = array();

        foreach($facultiesData as $facultyData) {
            $faculty = new Faculty();
            $faculty->setName($facultyData[0]);
            $this->setReference('faculty-' . $facultyData[0], $faculty);

            $faculties[] = $faculty;
        }

        return $faculties;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return -20;
    }
}
