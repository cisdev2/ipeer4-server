<?php

namespace Ipeer\CourseBundle\Controller;

use Ipeer\CourseBundle\Entity\Faculty;
use Ipeer\CourseBundle\Entity\Course;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ipeer\CourseBundle\Entity\Department;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Department controller.
 *
 * @Route("/faculties/{faculty}/departments")
 */
class DepartmentController extends Controller
{
    /**
     * List all the departments in a faculty.
     *
     * @param Faculty $faculty
     *
     * @return array
     *
     * @param Faculty $faculty
     * @ParamConverter("faculty", class="IpeerCourseBundle:Faculty")
     *
     * @ApiDoc(resource=true)
     * @Route("", name="department")
     *
     * @Method("GET")
     */
    public function indexAction(Faculty $faculty)
    {
        return array(
            'departments' => $faculty->getDepartments(),
        );
    }

    /**
     * Finds and displays a Department entity.
     *
     * @param Department $department
     * @ParamConverter("department", class="IpeerCourseBundle:Department")
     *
     * @param Faculty $faculty
     * @ParamConverter("faculty", class="IpeerCourseBundle:Faculty")
     *
     * @return Department
     *
     * @Route("/{id}", name="department_show")
     * @ApiDoc()
     * @Method("GET")
     */
    public function showAction(Department $department, Faculty $faculty)
    {
        if ($department->getFaculty() != $faculty) {
            throw new NotFoundHttpException();
        }
        return $department;
    }

    /**
     * Creates a new Department entity.
     *
     * @param Department $department
     * @ParamConverter("department", converter="fos_rest.request_body")
     *
     * @param Faculty $faculty
     * @ParamConverter("faculty", class="IpeerCourseBundle:Faculty")
     *
     * @return Department
     *
     * @param Department $department
     * @param Faculty    $faculty
     *
     * @Method("POST")
     * @Route("", name="department_create")
     * @ApiDoc()
     */
    public function createAction(Department $department, Faculty $faculty)
    {
        if ($department->getFaculty() != $faculty) {
            throw new NotFoundHttpException();
        }

        $faculty->addDepartment($department);
        $em = $this->getDoctrine()->getManager();
        $em->persist($department);
        $em->flush();

        return $department;
    }

    /**
     * Edits an existing Department entity.
     *
     * @param Department $targetDepartment
     * @ParamConverter("targetDepartment", class="IpeerCourseBundle:Department")
     *
     * @param Department $department
     * @ParamConverter("department", converter="fos_rest.request_body")
     *
     * @param Faculty $faculty
     * @ParamConverter("faculty", class="IpeerCourseBundle:Faculty")
     *
     * @return Department
     *
     * @Route("/{id}", name="department_update")
     * @ApiDoc()
     * @Method("POST")
     */
    public function updateAction(Department $targetDepartment, Department $department, Faculty $faculty)
    {
        if ($department->getFaculty() != $faculty) {
            throw new NotFoundHttpException();
        }

        $targetDepartment->setName($department->getName());

        $this->getDoctrine()->getManager()->flush();

        return $targetDepartment;
    }

    /**
     * Deletes a Department entity.
     *
     * @param Department $department
     * @ParamConverter("department", class="IpeerCourseBundle:Department")
     *
     * @param $faculty
     * @ParamConverter("faculty", class="IpeerCourseBundle:Faculty")
     *
     * @Rest\View(statusCode=204)
     *
     * @Route("/{id}", name="department_delete")
     * @ApiDoc()
     * @Method("DELETE")
     */
    public function deleteAction(Department $department, Faculty $faculty)
    {
        if ($department->getFaculty() != $faculty) {
            throw new NotFoundHttpException();
        }

        $faculty->removeDepartment($department);

        $em = $this->getDoctrine()->getManager();
        $em->remove($department);
        $em->flush();
    }

    /**
     * Adds a department to a faculty.
     *
     * @param Department $department
     * @ParamConverter("department", class="IpeerCourseBundle:Department")
     *
     * @param Faculty $faculty
     * @ParamConverter("faculty", class="IpeerCourseBundle:Faculty")
     *
     * @param Course $course
     * @ParamConverter("course", class="IpeerCourseBundle:Course")
     *
     * @Rest\View(statusCode=204)
     *
     * @Route("{id}/courses/{course}", name="department_course_add")
     * @ApiDoc()
     * @Method("POST")
     */
    public function addCourseAction(Department $department, Faculty $faculty, Course $course)
    {
        if ($department->getFaculty() != $faculty) {
            throw new NotFoundHttpException();
        }

        $department->addCourse($course);

        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * Removes a department from a faculty.
     *
     * @param Department $department
     * @ParamConverter("department", class="IpeerCourseBundle:Department")
     *
     * @param Faculty $faculty
     * @ParamConverter("faculty", class="IpeerCourseBundle:Faculty")
     *
     * @param Course $course
     * @ParamConverter("course", class="IpeerCourseBundle:Course")
     *
     * @Rest\View(statusCode=204)
     *
     * @Route("{id}/courses/{course}", name="department_course_delete")
     * @ApiDoc()
     * @Method("DELETE")
     */
    public function removeCourseAction(Department $department, Faculty $faculty, Course $course)
    {
        if ($department->getFaculty() != $faculty) {
            throw new NotFoundHttpException();
        }
        if($course->getDepartment() != $department) {
            throw new NotFoundHttpException();
        }

        $department->removeCourse($course);
        $this->getDoctrine()->getManager()->flush();
    }
}
