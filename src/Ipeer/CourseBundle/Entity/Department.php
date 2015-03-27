<?php

namespace Ipeer\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Department
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ipeer\CourseBundle\Entity\DepartmentRepository")
 * @ExclusionPolicy("all")
 */
class Department
{
    /**
     * @var integer
     *
     * @Expose
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
     *
     * @Expose()
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Faculty", inversedBy="departments")
     * @ORM\JoinColumn(name="faculty_id", referencedColumnName="id")
     *
     **/
    private $faculty;

    /**
     * @ORM\OneToMany(targetEntity="Course", mappedBy="department")
     **/
    private $courses;

    /**
     * Constructor
     */
    public function __construct() {
        $this->courses = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return integer
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set name
     *
     * @param string $name
     * @return Department
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Faculty $faculty
     * @return Department
     */
    public function setFaculty(Faculty $faculty) {
        $this->faculty = $faculty;
        $faculty->addDepartment($this);
        return $this;
    }

    /**
     * @return Faculty
     */
    public function getFaculty() {
        return $this->faculty;
    }

    /**
     * @param Course $course
     * @return Department
     */
    public function addCourse(Course $course)
    {
        $this->getCourses()->add($course);
        $course->setDepartment($this);

        return $this;
    }

    /**
     * @param Course $course
     *
     * @return Department
     */
    public function removeCourse(Course $course)
    {
        $this->getCourses()->removeElement($course);
        $course->setDepartment(null);

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourses()
    {
        if(null === $this->courses) {
            // needed if object is deserialized and constructor gets bypassed
            $this->courses = new ArrayCollection();
        }
        return $this->courses;
    }
}
