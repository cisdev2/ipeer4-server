<?php

namespace Ipeer\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Ipeer\UserBundle\Entity\User;

/**
 * Enrollment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ipeer\CourseBundle\Entity\EnrollmentRepository")
 */
class Enrollment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Course
     *
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="enrollments")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $course;

    /**
     * @var ArrayCollection|CourseGroup[]
     *
     * @ORM\ManyToMany(targetEntity="CourseGroup", inversedBy="enrollments")
     * @ORM\JoinTable(name="enrollments_groups")
     */
    private $courseGroups;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Ipeer\UserBundle\Entity\User", inversedBy="enrollments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /*
     * Role-related fields
     *
     * See role methods later in this class
     */
    /**
     * @var integer
     * @ORM\Column(name="role_id", type="smallint")
     */
    private $courseRole;

    /**
     * @var array
     */
    private static $courseRoles = array(
        0 => "Student",
        1 => "Tutor",
        2 => "Instructor",
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->courseGroups = new ArrayCollection();
    }

    /**
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Course $course
     * @return Enrollment
     */
    public function setCourse(Course $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * @return Course
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param User $user
     * @return Enrollment
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * @param CourseGroup $courseGroup
     * @return Enrollment
     */
    public function addCourseGroup(CourseGroup $courseGroup)
    {
        $this->courseGroups[] = $courseGroup;

        return $this;
    }

    /**
     * @param CourseGroup $courseGroup
     */
    public function removeCourseGroup(CourseGroup $courseGroup)
    {
        $this->courseGroups->removeElement($courseGroup);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCourseGroups()
    {
        return $this->courseGroups;
    }

    /*
     * Role related logic
     * =======================================================
     *
     * Depending on development flow, Role could be turned into its own class
     */

    /**
     * @param integer $role
     * @return Enrollment
     */
    public function setRoleById($role)
    {
        if(isset(self::$courseRoles[$role])) {
            $this->courseRole = $role;
            return $this;
        }
        throw new \InvalidArgumentException("Invalid role id");
    }

//    Instead of this, use setRoleById(roleStringToId($string))
//    We don't want multiple setters to throw exceptions
//
//    /**
//     * @param string $role
//     * @return Enrollment
//     */
//    public function setRoleByString($role)
//    {
//
//      ???
//
//    }

    /**
     * @return integer
     */
    public function getRoleId()
    {
        return $this->courseRole;
    }

    /**
     * @param integer $id
     * @return string
     */
    public static function roleIdToString($id) {
        if(isset(self::$courseRoles[$id])) {
            return self::$courseRoles[$id];
        }
        throw new \InvalidArgumentException("Invalid role id");
    }

    /**
     * @param string $string
     * @return integer
     */
    public static function roleStringToId($string) {
        $id = array_search($string, self::$courseRoles);
        if($id !== FALSE) {
            return $id;
        }
        throw new \InvalidArgumentException("Invalid role name");
    }

    /**
     * @return array
     */
    public static function getRoles() {
        return self::$courseRoles; // returned by copy, so no need to worry about caller changing this
    }

}
