<?php

namespace Ipeer\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\ReadOnly;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table("ipeer_user")
 * @ORM\Entity(repositoryClass="Ipeer\UserBundle\Entity\UserRepository")
 *
 * @ExclusionPolicy("all")
 */
class User
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
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="firstName", type="string", length=255)
     *
     * @Expose
     */
    private $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="lastName", type="string", length=255)
     *
     * @Expose
     */
    private $lastName;

    /**
     * @var string
     *
     * @Assert\Email()
     *
     * @ORM\Column(name="email", type="string", length=255)
     *
     * @Expose
     */
    private $email;


    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="Ipeer\CourseBundle\Entity\Enrollment", mappedBy="user")
     */
    private $enrollments;

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
     * Set id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
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
     * @param \Ipeer\CourseBundle\Entity\Enrollment $enrollments
     * @return User
     */
    public function addEnrollment(\Ipeer\CourseBundle\Entity\Enrollment $enrollments)
    {
        $this->enrollments[] = $enrollments;

        return $this;
    }

    /**
     * Remove enrollments
     *
     * @param \Ipeer\CourseBundle\Entity\Enrollment $enrollments
     */
    public function removeEnrollment(\Ipeer\CourseBundle\Entity\Enrollment $enrollments)
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
}
