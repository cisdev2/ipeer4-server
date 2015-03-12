<?php

namespace Ipeer\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CourseGroup
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="UBC\iPeer\CourseBundle\Entity\CourseGroupRepository")
 */
class CourseGroup
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
     * @var
     *
     * @ORM\ManyToMany(targetEntity="Enrollment", mappedBy="courseGroups")
     */
    private $enrollments;

    /**
     * @var Course
     *
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="courseGroups")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id")
     */
    private $course;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enrollments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add enrollments
     *
     * @param \UBC\iPeer\CourseBundle\Entity\Enrollment $enrollments
     * @return CourseGroup
     */
    public function addEnrollment(\UBC\iPeer\CourseBundle\Entity\Enrollment $enrollments)
    {
        $this->enrollments[] = $enrollments;

        return $this;
    }

    /**
     * Remove enrollments
     *
     * @param \UBC\iPeer\CourseBundle\Entity\Enrollment $enrollments
     */
    public function removeEnrollment(\UBC\iPeer\CourseBundle\Entity\Enrollment $enrollments)
    {
        $this->enrollments->removeElement($enrollments);
    }

    /**
     * Get enrollments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEnrollments()
    {
        return $this->enrollments;
    }

    /**
     * Set course
     *
     * @param \UBC\iPeer\CourseBundle\Entity\Course $course
     * @return CourseGroup
     */
    public function setCourse(\UBC\iPeer\CourseBundle\Entity\Course $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \UBC\iPeer\CourseBundle\Entity\Course 
     */
    public function getCourse()
    {
        return $this->course;
    }
}
