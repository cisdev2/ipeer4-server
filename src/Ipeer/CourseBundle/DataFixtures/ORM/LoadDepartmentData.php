<?php

namespace Ipeer\CourseBundle\DataFixtures\ORM;

use Ipeer\ApiUtilityBundle\DataFixtures\ORM\DataLoadingFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Ipeer\CourseBundle\Entity\Department;

class LoadDepartmentData extends DataLoadingFixture implements OrderedFixtureInterface {

    const NUMBER_OF_FACULTIES = 4;
    const NUMBER_OF_DEPARTMENTS = 5;

    /**
     * {@inheritdoc}
     */
    protected function makeData()
    {
        $departmentsData = array(
            array("MECH", "Applied Science"), // id = 1, index = 0
            array("CPSC", "Science"),
            array("MATH", "Science"), // id = 3, index = 1
            array("ENGL", "Arts"),
            array("COMM", "Business"), // id = 5, index = 4
        );

        $departments = array();

        foreach($departmentsData as $departmentData) {
            $department = new Department();
            $department->setName($departmentData[0]);
            $department->setFaculty($this->getReference('faculty-' . $departmentData[1]));
            $this->setReference('department-' . $departmentData[0], $department);

            $departments[] = $department;
        }

        return $departments;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return -10;
    }
}
