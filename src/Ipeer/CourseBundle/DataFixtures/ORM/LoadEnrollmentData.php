<?php

namespace Ipeer\CourseBundle\DataFixtures\ORM;

use Ipeer\ApiUtilityBundle\DataFixtures\ORM\SingletonDataFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Ipeer\CourseBundle\Entity\Enrollment;

class LoadEnrollmentData extends SingletonDataFixture implements OrderedFixtureInterface {

    /**
     * {@inheritdoc}
     */
    protected function makeData()
    {
        $instructor = Enrollment::INSTRUCTOR_ROLE;
        $student = Enrollment::STUDENT_ROLE;
        $tutor = Enrollment::TUTOR_ROLE;

        $enrolsData = array(
            // super admins shouldn't need specific access, but added in the case it comes up in some tests
            array("APSC201", "sudo01@ipeer.ubc", $instructor),
            // case where a super admin can also serve as a tutor
            array("MECH220", "sudo02@ipeer.ubc", $tutor),

            // faculty admins added redundantly
            // (eg. may happen when importing courses from an LMS)
            array("APSC201", "engineeradmin@ipeer.ubc", $instructor),
            array("CPSC312", "scienceadmin@ipeer.ubc", $instructor),
            // faculty admins with different role outside their faculty
            array("MATH342", "artsadmin@ipeer.ubc", $student),
            array("ARTS001", "businessadmin@ipeer.ubc", $tutor),
            array("CPSC312", "businessadmin@ipeer.ubc", $instructor),

            // instructors added to their courses
            // note some courses have 2 instructors
            array("APSC201", "apscInstr@ipeer.ubc", $instructor),
            array("MECH220", "mechInstr@ipeer.ubc", $instructor),
            array("MECH220", "apscInstr@ipeer.ubc", $instructor),
            array("CPSC312", "cpscInstr@ipeer.ubc", $instructor),
            array("CPSC312", "apscInstr@ipeer.ubc", $instructor),
            array("MATH342", "mathInstr@ipeer.ubc", $instructor),
            array("ENGL112", "englInstr@ipeer.ubc", $instructor),
            array("ENGL112", "artsInstr@ipeer.ubc", $instructor),
            array("ARTS001", "artsInstr@ipeer.ubc", $instructor),
            array("COMM335", "commInstr@ipeer.ubc", $instructor),

            // instructors with different role outside their course
            array("CPSC312", "englInstr@ipeer.ubc", $student),
            array("ARTS001", "commInstr@ipeer.ubc", $tutor),
            array("MATH342", "commInstr@ipeer.ubc", $student),

            // tutors added to courses
            // some courses have >1, some have none
            array("APSC201", "tutor01@ipeer.ubc", $tutor),
            array("MECH220", "tutor02@ipeer.ubc", $tutor),
            array("MECH220", "tutor03@ipeer.ubc", $tutor),
            array("MECH220", "tutor04@ipeer.ubc", $tutor),
            array("CPSC312", "tutor01@ipeer.ubc", $tutor),
            array("CPSC312", "tutor02@ipeer.ubc", $tutor),
            array("MATH342", "tutor03@ipeer.ubc", $tutor),

            // tutors who are also students
            array("ENGL112", "tutor04@ipeer.ubc", $student),
            array("ARTS001", "tutor03@ipeer.ubc", $student),

            // plain old students
            array("APSC201", "student01@ipeer.ubc" , $student),
            array("APSC201", "student02@ipeer.ubc" , $student),
            array("APSC201", "student03@ipeer.ubc" , $student),
            array("APSC201", "student04@ipeer.ubc" , $student),
            array("APSC201", "student05@ipeer.ubc" , $student),
            array("APSC201", "student06@ipeer.ubc" , $student),
            array("MECH220", "student07@ipeer.ubc" , $student),
            array("MECH220", "student08@ipeer.ubc" , $student),
            array("MECH220", "student09@ipeer.ubc" , $student),
            array("MECH220", "student10@ipeer.ubc" , $student),
            array("MECH220", "student11@ipeer.ubc" , $student),
            array("MECH220", "student12@ipeer.ubc" , $student),
            array("MECH220", "student13@ipeer.ubc" , $student),
            array("MECH220", "student14@ipeer.ubc" , $student),
            array("MECH220", "student15@ipeer.ubc" , $student),
            array("CPSC312", "student16@ipeer.ubc" , $student),
            array("CPSC312", "student17@ipeer.ubc" , $student),
            array("CPSC312", "student18@ipeer.ubc" , $student),
            array("MATH342", "student16@ipeer.ubc" , $student),
            array("MATH342", "student17@ipeer.ubc" , $student),
            array("MATH342", "student18@ipeer.ubc" , $student),
            array("ENGL112", "student19@ipeer.ubc" , $student),
            array("ENGL112", "student20@ipeer.ubc" , $student),
            array("ENGL112", "student16@ipeer.ubc" , $student),
            array("ARTS001", "student19@ipeer.ubc" , $student),
            array("ARTS001", "student20@ipeer.ubc" , $student),
            array("ARTS001", "student18@ipeer.ubc" , $student),
            array("COMM335", "student19@ipeer.ubc" , $student),
            array("COMM335", "student17@ipeer.ubc" , $student),
            array("COMM335", "student18@ipeer.ubc" , $student),
        );

        $enrols = array();

        foreach($enrolsData as $enrolData) {
            $enrol = new Enrollment();
            $enrol->setCourse($this->getReference('course-' . $enrolData[0]));
            $enrol->setUser($this->getReference('user-' . $enrolData[1]));
            $enrol->setRoleById($enrolData[2]);
            $this->setReference('enrol-' . $enrolData[0] . '-' . $enrolData[1], $enrol);

            $enrols[] = $enrol;
        }

        return $enrols;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 20;
    }
}
