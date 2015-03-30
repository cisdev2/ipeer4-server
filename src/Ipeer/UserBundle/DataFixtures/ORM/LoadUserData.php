<?php

namespace Ipeer\UserBundle\DataFixtures\ORM;

use Ipeer\UserBundle\Entity\User;
use Ipeer\ApiUtilityBundle\DataFixtures\ORM\DataLoadingFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadUserData extends DataLoadingFixture implements OrderedFixtureInterface
{

    const NUMBER_OF_USERS = 37;

    /**
     * {@inheritdoc}
     */
    protected function makeData() {

        $usersData = array(

            array("Sudo1", "SuperAdmin01", "sudo01@ipeer.ubc"), // id = 1; index = 0
            array("Sudo2", "SuperAdmin02", "sudo02@ipeer.ubc"),

            array("Science", "Admin01", "scienceadmin@ipeer.ubc", "Science"),
            array("Engineering", "Admin02", "engineeradmin@ipeer.ubc", "Applied Science"),
            array("Arts", "Admin03", "artsadmin@ipeer.ubc", "Arts"),
            array("Business", "Admin04", "businessadmin@ipeer.ubc", "Business"),

            array("APSC", "Instructor01", "apscInstr@ipeer.ubc"), // id = 7; index = 6
            array("MECH", "Instructor02", "mechInstr@ipeer.ubc"),
            array("CPSC", "Instructor04", "cpscInstr@ipeer.ubc"),
            array("MATH", "Instructor05", "mathInstr@ipeer.ubc"),
            array("ENGL", "Instructor07", "englInstr@ipeer.ubc"),
            array("ARTS", "Instructor08", "artsInstr@ipeer.ubc"),
            array("COMM", "Instructor10", "commInstr@ipeer.ubc"),

            array("Tutour", "Tutor01", "tutor01@ipeer.ubc"), // id = 14; index = 13
            array("Tuteur", "Tutor02", "tutor02@ipeer.ubc"),
            array("Tutoor", "Tutor03", "tutor03@ipeer.ubc"),
            array("Tutoar", "Tutor04", "tutor04@ipeer.ubc"),

            array("Kirk", "Student01", "student01@ipeer.ubc"), // id = 18; index = 17
            array("Spock", "Student02", "student02@ipeer.ubc"),
            array("McCoy", "Student03", "student03@ipeer.ubc"),
            array("Scott", "Student04", "student04@ipeer.ubc"),
            array("Uhura", "Student05", "student05@ipeer.ubc"),
            array("Sulu", "Student06", "student06@ipeer.ubc"),
            array("Frodo", "Student07", "student07@ipeer.ubc"),
            array("Bilbo", "Student08", "student08@ipeer.ubc"),
            array("Samwise", "Student09", "student09@ipeer.ubc"),
            array("Gandalf", "Student10", "student10@ipeer.ubc"),
            array("Aragorn", "Student11", "student11@ipeer.ubc"),
            array("Legolas", "Student12", "student12@ipeer.ubc"),
            array("Gimli", "Student13", "student13@ipeer.ubc"),
            array("Aang", "Student14", "student14@ipeer.ubc"),
            array("Katara", "Student15", "student15@ipeer.ubc"),
            array("Sokka", "Student16", "student16@ipeer.ubc"),
            array("Toph", "Student17", "student17@ipeer.ubc"),
            array("Zuko", "Student18", "student18@ipeer.ubc"),
            array("Iroh", "Student19", "student19@ipeer.ubc"),
            array("Bumi", "Student20", "student20@ipeer.ubc"),
        );

        $users = array();

        foreach($usersData as $userData) {
            $user = new User();
            $user->setFirstName($userData[0]);
            $user->setLastName($userData[1]);
            $user->setEmail($userData[2]);

            if(isset($userData[3])) {
                $user->addFaculty($this->getReference('faculty-' . $userData[3]));
            }

            $this->setReference('user-' . $userData[2], $user);

            $users[] = $user;
        }

        return $users;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 0;
    }
}