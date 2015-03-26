<?php

namespace Ipeer\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Inline;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Course
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ipeer\CourseBundle\Entity\CourseRepository")
 *
 * @ExclusionPolicy("all")
 */
class Course
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose()
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\NotBlank()
     *
     * @Expose()
     */
    private $name;

    /**
     * @var ArrayCollection|Enrollment[]
     *
     * @ORM\OneToMany(targetEntity="Enrollment", mappedBy="course")
     *
     */
    private $enrollments;

    /**
     * @var ArrayCollection|CourseGroup[]
     *
     * @ORM\OneToMany(targetEntity="CourseGroup", mappedBy="course")
     */
    private $courseGroups;

    /**
     * @var Department
     *
     * @ORM\ManyToOne(targetEntity="Department", inversedBy="courses")
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id")
     *
     * @Expose
     *
     **/
    private $department;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enrollments = new ArrayCollection();
        $this->courseGroups = new ArrayCollection();
    }

    /**
     * @param integer $id
     * @return Course
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
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
        $this->getEnrollments()->add($enrollment);

        return $this;
    }

    /**
     * @param Enrollment $enrollment
     */
    public function removeEnrollment(Enrollment $enrollment)
    {
        $this->getEnrollments()->removeElement($enrollment);
    }

    /**
     * @return ArrayCollection|Enrollment[]
     */
    public function getEnrollments()
    {
        if(null === $this->enrollments) {
            // needed if object is deserialized and constructor get bypassed
            $this->enrollments = new ArrayCollection();
        }
        return $this->enrollments;
    }

    /**
     * @param CourseGroup $courseGroup
     * @return Course
     */
    public function addCourseGroup(CourseGroup $courseGroup)
    {
        $this->getCourseGroups()->add($courseGroup);
        $courseGroup->setCourse($this);

        return $this;
    }

    /**
     * @param CourseGroup $courseGroup
     */
    public function removeCourseGroup(CourseGroup $courseGroup)
    {
        $this->getCourseGroups()->removeElement($courseGroup);
    }

    /**
     * @return ArrayCollection|CourseGroup[]
     */
    public function getCourseGroups()
    {
        if(null === $this->courseGroups) {
            // needed if object is deserialized and constructor get bypassed
            $this->courseGroups = new ArrayCollection();
        }

        return $this->courseGroups;
    }

    /**
     * @param Department $department
     * @return Course
     */
    public function setDepartment(Department $department) {
        $this->department = $department;

        return $this;
    }

    /**
     * @return Course
     */
    public function getDepartment() {
        return $this->department;
    }
}
