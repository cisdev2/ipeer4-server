<?php

namespace Ipeer\CourseBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * EnrollmentRepository
 */
class EnrollmentRepository extends EntityRepository
{
    /**
     * @param integer $userId
     * @param integer $courseId
     * @return null|Enrollment
     */
    public function getEnrollmentByUserCourse($userId, $courseId)
    {
        return $this->findOneBy(array(
            'course' => $courseId,
            'user' => $userId
        ));
    }
}
