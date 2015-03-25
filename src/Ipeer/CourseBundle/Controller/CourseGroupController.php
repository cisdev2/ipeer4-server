<?php

namespace Ipeer\CourseBundle\Controller;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Ipeer\CourseBundle\Entity\CourseGroup;
use FOS\RestBundle\Controller\Annotations as Rest;
use Ipeer\CourseBundle\Entity\Course;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Ipeer\UserBundle\Entity\User;

/**
 * Group controller.
 *
 * @Route("/courses/{course}/groups")
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
     * @ApiDoc(resource=true)
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
     * @param Course $course
     * @ParamConverter("course", class="IpeerCourseBundle:Course")
     *
     * @return CourseGroup
     *
     * @ApiDoc()
     * @Route("", name="group_create")
     * @Method("POST")
     */
    public function createAction(Course $course, CourseGroup $group)
    {
        $course->addCourseGroup($group);
        $em = $this->getDoctrine()->getManager();
        $em->persist($group);
        $em->flush();

        return $group->getInfoandMembers();
    }

    /**
     * Finds and displays a Group entity.
     *
     * @param CourseGroup $group
     * @ParamConverter("group", class="IpeerCourseBundle:CourseGroup")
     *
     * @param Course $course
     * @ParamConverter("course", class="IpeerCourseBundle:Course")
     *
     * @return CourseGroup
     *
     * @ApiDoc()
     * @Route("/{id}", name="group_show")
     * @Method("GET")
     */
    public function showAction(CourseGroup $group, Course $course)
    {
        if($group->getCourse() == $course) {
            return $group->getInfoandMembers();
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * Edits an existing Group entity.
     *
     * @param CourseGroup $targetGroup
     * @ParamConverter("targetGroup", class="IpeerCourseBundle:CourseGroup")
     *
     * @param Course $course
     * @ParamConverter("course", class="IpeerCourseBundle:Course")
     *
     * @param CourseGroup $group
     * @ParamConverter("group", converter="fos_rest.request_body")
     *
     * @return CourseGroup
     *
     * @ApiDoc()
     * @Route("/{id}", name="group_update")
     * @Method("POST")
     */
    public function updateAction(CourseGroup $targetGroup, Course $course, CourseGroup $group)
    {
        if($targetGroup->getCourse() == $course) {
            // we need to modify targetGroup directly to avoid losing group data
            // so we go field-by-field:
            // - name
            // ... other fields go here if added
            $targetGroup->setName($group->getName());

            $this->getDoctrine()->getManager()->flush();

            return $targetGroup->getInfoandMembers();
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * Deletes a Group entity.
     *
     * @param CourseGroup $group
     * @ParamConverter("group", class="IpeerCourseBundle:CourseGroup")
     *
     * @param Course $course
     * @ParamConverter("course", class="IpeerCourseBundle:Course")
     *
     * @Rest\View(statusCode=204)
     *
     * @ApiDoc()
     * @Route("/{id}", name="group_delete")
     * @Method("DELETE")
     */
    public function deleteAction(CourseGroup $group, Course $course)
    {
        if($group->getCourse() == $course) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($group);
            $em->flush();
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * Add a member to the group.
     *
     *
     * @param Course $course
     * @ParamConverter("course", class="IpeerCourseBundle:Course")
     *
     * @param CourseGroup $group
     * @ParamConverter("group", class="IpeerCourseBundle:CourseGroup")
     *
     * @param User $user
     * @ParamConverter("user", class="IpeerUserBundle:User")
     *
     * @Rest\View(statusCode=204)
     *
     * @ApiDoc()
     * @Route("/{id}/addMember/{user}", name="group_member_add")
     * @Method("POST")
     */
    public function addMemberAction(Course $course, CourseGroup $group,  User $user)
    {
        $enrollment = $this->getDoctrine()->getRepository('IpeerCourseBundle:Enrollment')->getEnrollmentByUserCourse($user->getId(), $course->getId());

        if($group->getCourse() == $course) {
            if($enrollment === null) {
                throw new BadRequestHttpException();
            }
            if(!$group->getEnrollments()->contains($enrollment)) {
                $group->addEnrollment($enrollment);
                $this->getDoctrine()->getManager()->flush();
            }
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * Removes a member from the group.
     *
     * @param CourseGroup $group
     * @ParamConverter("group", class="IpeerCourseBundle:CourseGroup")
     *
     * @param Course $course
     * @ParamConverter("course", class="IpeerCourseBundle:Course")
     *
     * @param User $user
     * @ParamConverter("course", class="IpeerCourseBundle:Course")
     *
     * @Rest\View(statusCode=204)
     *
     * @ApiDoc()
     * @Route("/{id}/removeMember/{user}", name="group_member_delete")
     * @Method("DELETE")
     */
    public function removeMemberAction(CourseGroup $group, Course $course, User $user)
    {
        $enrollment = $this->getDoctrine()->getRepository('IpeerCourseBundle:Enrollment')->getEnrollmentByUserCourse($user->getId(), $course->getId());

        if($group->getCourse() === $course
            && $enrollment !== null
            && $group->getEnrollments()->contains($enrollment)) {
            $group->removeEnrollment($enrollment);
            $this->getDoctrine()->getManager()->flush();
        } else {
            throw new NotFoundHttpException();
        }
    }
}
