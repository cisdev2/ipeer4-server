<?php

namespace Ipeer\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * CourseGroup
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ipeer\CourseBundle\Entity\CourseGroupRepository")
 *
 * @ExclusionPolicy("all")
 */
class CourseGroup
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
     * Constructor
     */
    public function __construct()
    {
        $this->enrollments = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @param integer $id
     * @return CourseGroup
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
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
     * Add enrollments
     *
     * @param Enrollment $enrollment
     * @return CourseGroup
     */
    public function addEnrollment(Enrollment $enrollment)
    {
        if($enrollment->isStudent() || $enrollment->isTutor()) {
            $this->getEnrollments()->add($enrollment);
            $enrollment->addCourseGroup($this);
            return $this;
        }
        throw new BadRequestHttpException();
    }

    /**
     * Remove enrollments
     *
     * @param Enrollment $enrollment
     */
    public function removeEnrollment(Enrollment $enrollment)
    {
        $this->getEnrollments()->removeElement($enrollment);
        $enrollment->removeCourseGroup($this);
    }

    /**
     * Get enrollments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEnrollments()
    {
        if(null === $this->enrollments) {
            // needed if object is deserialized and constructor get bypassed
            $this->enrollments = new \Doctrine\Common\Collections\ArrayCollection();
        }
        return $this->enrollments;
    }

    /**
     * Set course
     *
     * @param Course $course
     * @return CourseGroup
     */
    public function setCourse(Course $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return Course
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Used in (JSON) serialization
     *
     * @return array
     */
    public function getInfoandMembers() {
        return array('group' => $this, 'members' => $this->getEnrollments());
    }
}
