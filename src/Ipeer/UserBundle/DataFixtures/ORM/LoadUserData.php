<?php

namespace Ipeer\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ipeer\UserBundle\Entity\User;

class LoadUserData implements FixtureInterface
{

    public static $users;

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

        self::$users = array($userAdmin, $userStudent, $userInstructor);

        $manager->persist($userAdmin);
        $manager->persist($userStudent);
        $manager->persist($userInstructor);
        $manager->flush();
    }
}