<?php

namespace Ipeer\CourseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Ipeer\UserBundle\Entity\User;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Faculty
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ipeer\CourseBundle\Entity\FacultyRepository")
 *
 * @ExclusionPolicy("all")
 */
class Faculty
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Expose
     */
    private $name;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Department", mappedBy="faculty")
     **/
    private $departments;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Ipeer\UserBundle\Entity\User", mappedBy="faculties")
     */
    private $users;

    /**
     * Constructor
     */
    public function __construct() {
        $this->departments = new ArrayCollection();
        $this->users = new ArrayCollection();
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
     * @return Faculty
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
     * @param Department $department
     * @return Faculty
     */
    public function addDepartment(Department $department)
    {
        $this->getDepartments()->add($department);

        return $this;
    }

    /**
     * @param Department $department
     *
     * @return Faculty
     */
    public function removeDepartment(Department $department)
    {
        $this->getDepartments()->removeElement($department);

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDepartments()
    {
        if(null === $this->departments) {
            // needed if object is deserialized and constructor gets bypassed
            $this->departments = new ArrayCollection();
        }
        return $this->departments;
    }

    /**
     * @param User $user
     * @return Faculty
     */
    public function addUser(User $user)
    {
        $this->getUsers()->add($user);

        return $this;
    }

    /**
     * @param User $user
     *
     * @return Faculty
     */
    public function removeUser(User $user)
    {
        $this->getUsers()->removeElement($user);

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        if(null === $this->users) {
            // needed if object is deserialized and constructor gets bypassed
            $this->users = new ArrayCollection();
        }
        return $this->users;
    }

    public function getInfoAndDepartments() {
        return array($this,$this->getDepartments());
    }
}
