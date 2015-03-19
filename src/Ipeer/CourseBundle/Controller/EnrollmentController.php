<?php

namespace Ipeer\CourseBundle\Controller;

use Ipeer\CourseBundle\Entity\Enrollment;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ipeer\CourseBundle\Entity\Course;
use FOS\RestBundle\Controller\Annotations as Rest;
use Ipeer\UserBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Enrollment Controller
 *
 * @Route("/courses/{course}/enrollments")
 */
class EnrollmentController extends Controller
{
    /**
     * Lists all enrollment entities.
     *
     * @param Course $course
     * @ParamConverter("course", class="IpeerCourseBundle:Course")
     *
     * @return array
     *
     * @ApiDoc(resource=true)
     *
     * @Route("", name="enrollment")
     * @Method("GET")
     */
    public function indexAction(Course $course)
    {
        return array(
            'course' => $course,
            'enrollments' => $course->getEnrollments(),
        );
    }

    /**
     * Creates or edits an enrollment entity.
     *
     * @param Course $course
     * @ParamConverter("course", class="IpeerCourseBundle:Course")
     *
     * @param User $user
     * @ParamConverter("user", class="IpeerUserBundle:User")
     *
     * @param Enrollment $enrollment
     * @ParamConverter("enrollment", converter="fos_rest.request_body")
     *
     * @Rest\View(statusCode=204)
     *
     * @ApiDoc()
     * @Route("/{user}", name="enrollment_update")
     * @Method("POST")
     */
    public function updateAction(Course $course, User $user, Enrollment $enrollment)
    {
        echo (string) $this->get('validator')->validate($enrollment) . "-=-";

        $existingEnrollment = $this->getDoctrine()->getRepository('IpeerCourseBundle:Enrollment')->getEnrollmentByUserCourse($user->getId(), $course->getId());

        if(null === $existingEnrollment) {
            $enrollment->setCourse($course);
            $enrollment->setUser($user);
        } else {
            $enrollment = $existingEnrollment->setRoleById($enrollment->getRoleId());
        }

        $em = $this->getDoctrine()->getManager();
        $em->merge($enrollment);
        $em->flush();
    }

    /**
     * Deletes an enrollment entity.
     *
     * @param Course $course
     * @ParamConverter("course", class="IpeerCourseBundle:Course")
     *
     * @param User $user
     * @ParamConverter("user", class="IpeerUserBundle:User")
     *
     * @Rest\View(statusCode=204)
     *
     * @ApiDoc()
     * @Route("/{user}", name="enrollment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Course $course, User $user)
    {
        $existingEnrollment = $this->getDoctrine()->getRepository('IpeerCourseBundle:Enrollment')->getEnrollmentByUserCourse($user->getId(), $course->getId());

        if(null === $existingEnrollment) {
            throw new NotFoundHttpException();
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($existingEnrollment);
        $em->flush();
    }

}
