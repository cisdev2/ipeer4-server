<?php

namespace Ipeer\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ipeer\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements FixtureInterface
{

    /**
     * @var User[]
     */
    private static $users;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $users = self::getUsers();

        for($i = 0; $i < count($users); $i++) {
            $manager->persist($users[$i]);
        }

        $manager->flush();
    }

    /**
     * @return User[]
     *
     * Ensure this function only gets called once
     */
    private static function makeUsers() {
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

        return array($userAdmin, $userStudent, $userInstructor);
    }

    /**
     * @return User[]
     *
     * If the users have not been created, create them
     * Otherwise return them
     *
     * This is needed because of liip_functional_test: cache_sqlite_db: true
     */
    public static function getUsers() {
        if(null == self::$users || count(self::$users) === 0) {
            self::$users = self::makeUsers();
        }
        return self::$users;
    }
}