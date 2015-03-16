<?php

namespace Ipeer\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ipeer\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements FixtureInterface
{

    static public $users = array(); // needs to be instantiated for self::$users line works

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userAdmin = new User();
        $userAdmin->setFirstName("Admin");
        $userAdmin->setLastName("Prime");
        $userAdmin->setEmail("admin@ipeer.ubc");

        $userStudent = new User();
        $userStudent->setFirstName("Student");
        $userStudent->setLastName("Alpha");
        $userStudent->setEmail("studenta@ipeer.ubc");

        $userInstructor = new User();
        $userInstructor->setFirstName("Instructor");
        $userInstructor->setLastName("Epsilon");
        $userInstructor->setEmail("instructor@ipeer.ubc");

        // push database
        $manager->persist($userAdmin);
        $manager->persist($userStudent);
        $manager->persist($userInstructor);
        $manager->flush();

        // make raw objects available outside this fixture
        $this->addReference("user-admin-prime", $userAdmin);
        $this->addReference("user-student-alpha", $userStudent);
        $this->addReference("user-instructor-epsilon", $userInstructor);
        self::$users = array($userAdmin, $userStudent, $userInstructor);


    }
}