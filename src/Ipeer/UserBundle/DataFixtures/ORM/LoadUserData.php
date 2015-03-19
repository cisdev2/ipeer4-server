<?php

namespace Ipeer\UserBundle\DataFixtures\ORM;

use Ipeer\UserBundle\Entity\User;
use Ipeer\ApiUtilityBundle\DataFixtures\ORM\SingletonDataFixture;

class LoadUserData extends SingletonDataFixture
{

    /**
     * @InheritDoc()
     */
    protected function makeData() {

        $usersData = array(
            array("Sudo1", "SuperAdmin01", "sudo01@ipeer.ubc"),
            array("Sudo2", "SuperAdmin02", "sudo02@ipeer.ubc"),

            array("Science", "Admin01", "science@ipeer.ubc"),
            array("Engineering", "Admin02", "engineer@ipeer.ubc"),
            array("Arts", "Admin03", "arts@ipeer.ubc"),
            array("Business", "Admin04", "business@ipeer.ubc"),

            array("APSC", "Instructor01", "apsc@ipeer.ubc"),
            array("MECH", "Instructor02", "mech@ipeer.ubc"),
            array("EECE", "Instructor03", "eece@ipeer.ubc"),
            array("CPSC", "Instructor04", "cpsc@ipeer.ubc"),
            array("MATH", "Instructor05", "math@ipeer.ubc"),
            array("BIOL", "Instructor06", "biol@ipeer.ubc"),
            array("ENGL", "Instructor07", "engl@ipeer.ubc"),
            array("ARTS", "Instructor08", "arts@ipeer.ubc"),
            array("SCIE", "Instructor09", "scie@ipeer.ubc"),
            array("COMM", "Instructor10", "comm@ipeer.ubc"),

            array("Tutour", "Tutor01", "tutor01@ipeer.ubc"),
            array("Tuteur", "Tutor02", "tutor02@ipeer.ubc"),
            array("Tutoor", "Tutor03", "tutor03@ipeer.ubc"),
            array("Tutoar", "Tutor04", "tutor04@ipeer.ubc"),
            array("Tuteor", "Tutor05", "tutor05@ipeer.ubc"),

            array("Kirk", "Student01", "student01@ubc.ca"),
            array("Spock", "Student02", "student02@ubc.ca"),
            array("McCoy", "Student03", "student03@ubc.ca"),
            array("Scott", "Student04", "student04@ubc.ca"),
            array("Uhura", "Student05", "student05@ubc.ca"),
            array("Sulu", "Student06", "student06@ubc.ca"),
            array("Chapel", "Student07", "student07@ubc.ca"),
            array("Checkov", "Student08", "student08@ubc.ca"),
            array("Rand", "Student09", "student09@ubc.ca"),
            array("Frodo", "Student10", "student10@ubc.ca"),
            array("Bilbo", "Student11", "student11@ubc.ca"),
            array("Samwise", "Student12", "student12@ubc.ca"),
            array("Meriadoc", "Student13", "student13@ubc.ca"),
            array("Peregrin", "Student14", "student14@ubc.ca"),
            array("Gandalf", "Student15", "student15@ubc.ca"),
            array("Aragorn", "Student16", "student16@ubc.ca"),
            array("Legolas", "Student17", "student17@ubc.ca"),
            array("Gimli", "Student18", "student18@ubc.ca"),
            array("Aang", "Student19", "student19@ubc.ca"),
            array("Katara", "Student20", "student20@ubc.ca"),
            array("Sokka", "Student21", "student21@ubc.ca"),
            array("Toph", "Student22", "student22@ubc.ca"),
            array("Appa", "Student23", "student23@ubc.ca"),
            array("Momo", "Student24", "student24@ubc.ca"),
            array("Zuko", "Student25", "student25@ubc.ca"),
            array("Iroh", "Student26", "student26@ubc.ca"),
            array("Korra", "Student27", "student27@ubc.ca"),
            array("Asami", "Student28", "student28@ubc.ca"),
            array("Bolin", "Student29", "student29@ubc.ca"),
            array("Mako", "Student30", "student30@ubc.ca")
        );

        $users = array();

        foreach($usersData as $userData) {
            $user = new User();
            $user->setFirstName($userData[0]);
            $user->setLastName($userData[1]);
            $user->setEmail($userData[2]);

            $users[] = $user;
        }

        return $users;
    }
}