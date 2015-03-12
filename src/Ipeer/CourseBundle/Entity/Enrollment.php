<?php

namespace Ipeer\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Enrollment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="UBC\iPeer\CourseBundle\Entity\EnrollmentRepository")
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
     * @var \Ipeer\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Ipeer\UserBundle\Entity\User", inversedBy="enrollments")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Role
     *
     * @ORM\ManyToOne(targetEntity="Role")
     */
    private $role;

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
     * @param \UBC\iPeer\UserBundle\Entity\User $user
     * @return Enrollment
     */
    public function setUser(\UBC\iPeer\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return \UBC\iPeer\UserBundle\Entity\User 
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

    /**
     * @param \UBC\iPeer\CourseBundle\Entity\Role $role
     * @return Enrollment
     */
    public function setRole(\UBC\iPeer\CourseBundle\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return \UBC\iPeer\CourseBundle\Entity\Role 
     */
    public function getRole()
    {
        return $this->role;
    }
}
