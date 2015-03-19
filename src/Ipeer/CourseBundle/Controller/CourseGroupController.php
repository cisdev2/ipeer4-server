<?php

namespace Ipeer\CourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Ipeer\CourseBundle\Entity\CourseGroup;
use FOS\RestBundle\Controller\Annotations as Rest;
use Ipeer\CourseBundle\Entity\Course;

/**
 * Group controller.
 *
 * @Route("courses/{course}/groups")
 */
class CourseGroupController extends Controller
{

    /**
     * Lists all Group entities.
     *
     * @param Course $course
     * @ParamConverter("course", class="IpeerCourseBundle:Course")
     *
     * @return array
     *
     * @ApiDoc(
     *  resource=true,
     *  statusCodes={200=""}
     * )
     *
     * @Route("", name="group")
     * @Method("GET")
     */
    public function indexAction(Course $course)
    {
        return array(
            'groups' => $course->getCourseGroups(),
        );
    }

    /**
     * Creates a new Group entity.
     *
     * @param CourseGroup $group
     * @ParamConverter("group", converter="fos_rest.request_body")
     *
     * @return CourseGroup
     *
     * @ApiDoc(statusCodes={200="",400=""})
     * @Route("", name="group_create")
     * @Method("POST")
     */
    public function createAction(CourseGroup $group)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($group);
        $em->flush();

        return $group;
    }

    /**
     * Finds and displays a Group entity.
     *
     * @param CourseGroup $group
     *
     * @return CourseGroup
     *
     * @ApiDoc(statusCodes={200="",404=""})
     * @Route("/{id}", name="group_show")
     * @Method("GET")
     */
    public function showAction(CourseGroup $group)
    {
        return $group;
    }

    /**
     * Edits an existing Group entity.
     *
     * @param CourseGroup $group The data the user submitted
     * @ParamConverter("group", converter="fos_rest.request_body")
     *
     * @param CourseGroup $id The id of the group to update
     * @ParamConverter("id", class="IpeerCourseBundle:CourseGroup")
     *
     * @return CourseGroup
     *
     * @ApiDoc(statusCodes={200="",400="",404=""})
     * @Route("/{id}", name="group_update")
     * @Method("POST")
     */
    public function updateAction(CourseGroup $group, CourseGroup $id)
    {
        // inject the id value from the URL
        // (ensures update instead of creating a new duplicate)
        $group->setId($id->getId());

        $em = $this->getDoctrine()->getManager();
        $em->merge($group);
        $em->flush();

        return $group;
    }

    /**
     * Deletes a Group entity.
     *
     * @param CourseGroup $group
     *
     * @Rest\View(statusCode=204)
     *
     * @ApiDoc(statusCodes={204="",404=""})
     * @Route("/{id}", name="group_delete")
     * @Method("DELETE")
     */
    public function deleteAction(CourseGroup $group)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($group);
        $em->flush();
    }
}
