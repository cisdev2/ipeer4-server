<?php

namespace Ipeer\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Ipeer\UserBundle\Entity\User;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Enrollment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ipeer\CourseBundle\Entity\EnrollmentRepository")
 *
 * @ExclusionPolicy("all")
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
     *
     * @Expose()
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
     *
     * @Expose()
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 0,
     *      max = 2
     * )
     */
    private $courseRole;

    /**
     * Possible course roles
     *
     * In the future, this might be abstracted to another class
     */
    const STUDENT_ROLE = 0;
    const TUTOR_ROLE = 1;
    const INSTRUCTOR_ROLE = 2;

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
        $this->getCourseGroups()->add($courseGroup);

        return $this;
    }

    /**
     * @param CourseGroup $courseGroup
     *
     * @return Enrollment
     */
    public function removeCourseGroup(CourseGroup $courseGroup)
    {
        $this->getCourseGroups()->removeElement($courseGroup);

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourseGroups()
    {
        if(null === $this->courseGroups) {
            // needed if object is deserialized and constructor gets bypassed
            $this->courseGroups = new \Doctrine\Common\Collections\ArrayCollection();
        }
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
        if($role < 0 || $role > 2) { // Somewhat "volatile"
            throw new \InvalidArgumentException("Invalid role id");
        }
        $this->courseRole = $role;
        return $this;
    }

    /**
     * @return integer
     */
    public function getRoleId()
    {
        return $this->courseRole;
    }

    /**
     * @return bool
     */
    public function isStudent() {
        return $this->courseRole === self::STUDENT_ROLE;
    }

    /**
     * @return bool
     */
    public function isInstructor() {
        return $this->courseRole === self::INSTRUCTOR_ROLE;
    }

    /**
     * @return bool
     */
    public function isTutor() {
        return $this->courseRole === self::TUTOR_ROLE;
    }

}
