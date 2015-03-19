<?php

namespace Ipeer\CourseBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ipeer\CourseBundle\Entity\Course;
use FOS\RestBundle\Controller\Annotations as Rest;


/**
 * Course controller.
 *
 * @Route("/courses")
 */
class CourseController extends Controller
{

    /**
     * Lists all Course entities.
     *
     * @ApiDoc(
     *  resource=true,
     *  statusCodes={200=""}
     * )
     *
     * @Route("", name="course")
     * @Method("GET")
     */
    public function indexAction()
    {
        return array(
            'courses' => $this->getDoctrine()->getRepository('IpeerCourseBundle:Course')->findAll(),
        );
    }

    /**
     * Creates a new Course entity.
     *
     * @param Course $course
     * @ParamConverter("course", converter="fos_rest.request_body")
     *
     * @return Course
     *
     * @ApiDoc()
     * @Route("", name="course_create")
     * @Method("POST")
     */
    public function createAction(Course $course)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($course);
        $em->flush();

        return $course;
    }

    /**
     * Finds and displays a Course entity.
     *
     * @param Course $course
     *
     * @return Course
     *
     * @ApiDoc()
     * @Route("/{id}", name="course_show")
     * @Method("GET")
     */
    public function showAction(Course $course)
    {
        return $course;
    }

    /**
     * Edits an existing Course entity.
     *
     * @param Course $course The data the user submitted
     * @ParamConverter("course", converter="fos_rest.request_body")
     *
     * @param Course $id The entity to be updated
     * @ParamConverter("id", class="IpeerCourseBundle:Course")
     *
     * @return Course
     *
     * @ApiDoc()
     * @Route("/{id}", name="course_update")
     * @Method("POST")
     */
    public function updateAction(Course $course, Course $id)
    {
        // inject the id value from the URL
        // (ensures update instead of creating a new duplicate)
        $course->setId($id->getId());

        $em = $this->getDoctrine()->getManager();
        $em->merge($course);
        $em->flush();

        return $course;
    }

    /**
     * Deletes a Course entity.
     *
     * @param Course $course
     *
     * @Rest\View(statusCode=204)
     *
     * @ApiDoc()
     * @Route("/{id}", name="course_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Course $course)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($course);
        $em->flush();
    }
}
