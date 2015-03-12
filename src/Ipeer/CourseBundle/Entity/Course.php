<?php

namespace Ipeer\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Course
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ipeer\CourseBundle\Entity\CourseRepository")
 */
class Course
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var ArrayCollection|Enrollment[]
     *
     * @ORM\OneToMany(targetEntity="Enrollment", mappedBy="course")
     */
    private $enrollments;

    /**
     * @var ArrayCollection|CourseGroup[]
     *
     * @ORM\OneToMany(targetEntity="CourseGroup", mappedBy="course")
     */
    private $courseGroups;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enrollments = new ArrayCollection();
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
     * @param string $name
     * @return Course
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Enrollment $enrollment
     * @return Course
     */
    public function addEnrollment(Enrollment $enrollment)
    {
        $this->enrollments[] = $enrollment;

        return $this;
    }

    /**
     * @param Enrollment $enrollment
     */
    public function removeEnrollment(Enrollment $enrollment)
    {
        $this->enrollments->removeElement($enrollment);
    }

    /**
     * @return ArrayCollection|Enrollment[]
     */
    public function getEnrollments()
    {
        return $this->enrollments;
    }

    /**
     * @param CourseGroup $courseGroup
     * @return Course
     */
    public function addCourseGroup(CourseGroup $courseGroup)
    {
        $this->courseGroups[] = $courseGroup;

        return $this;
    }

    /**
     * @param CourseGroup $courseGroups
     */
    public function removeCourseGroup(CourseGroup $courseGroups)
    {
        $this->courseGroups->removeElement($courseGroups);
    }

    /**
     * @return ArrayCollection|CourseGroup[]
     */
    public function getCourseGroups()
    {
        return $this->courseGroups;
    }
}
