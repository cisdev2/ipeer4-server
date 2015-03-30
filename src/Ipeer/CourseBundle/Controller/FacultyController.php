<?php

namespace Ipeer\CourseBundle\Controller;


use Ipeer\UserBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ipeer\CourseBundle\Entity\Faculty;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Faculty controller.
 *
 * @Route("/faculties")
 */
class FacultyController extends Controller
{

    /**
     * Lists all Faculty entities.
     *
     * @ApiDoc(
     *  resource=true
     * )
     *
     * @Route("", name="faculty")
     * @Method("GET")
     */
    public function indexAction()
    {
        return array(
            'faculties' => $this->getDoctrine()->getRepository('IpeerCourseBundle:Faculty')->findAll(),
        );
    }

    /**
     * Creates a new Faculty entity.
     *
     * @param Faculty $faculty
     * @ParamConverter("faculty", converter="fos_rest.request_body")
     *
     * @return Faculty
     *
     * @ApiDoc()
     * @Route("", name="faculty_create")
     * @Method("POST")
     */
    public function createAction(Faculty $faculty) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($faculty);
        $em->flush();

        return $faculty;
    }

    /**
     * Finds and displays a Faculty entity.
     *
     * @param Faculty $faculty
     *
     * @return Faculty
     *
     * @ApiDoc()
     * @Route("/{id}", name="faculty_show")
     * @Method("GET")
     */
    public function showAction(Faculty $faculty)
    {
        return $faculty;
    }

    /**
     * Edits an existing Faculty entity.
     *
     * @param Faculty $faculty
     * @ParamConverter("faculty", class="IpeerCourseBundle:Faculty")
     *
     * @param Faculty $inputFaculty
     * @ParamConverter("inputFaculty", converter="fos_rest.request_body")
     *
     * @return Faculty
     *
     * @ApiDoc()
     * @Route("/{id}", name="faculty_update")
     * @Method("POST")
     */
    public function updateAction(Faculty $faculty, Faculty $inputFaculty) {
        // we need to modify $faculty directly to avoid department data
        // go field by field
        $faculty->setName($inputFaculty->getName());
        $this->getDoctrine()->getManager()->flush();
        return $faculty;
    }

    /**
     * Deletes a Faculty entity.
     *
     * @param Faculty $faculty
     *
     * @Rest\View(statusCode=204)
     *
     * @ApiDoc()
     * @Route("/{id}", name="faculty_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Faculty $faculty)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($faculty);
        $em->flush();
    }

    /**
     * Adds a user to the faculty.
     *
     * @param Faculty $faculty
     * @ParamConverter("faculty", class="IpeerCourseBundle:Faculty")
     *
     * @param User $user
     * @ParamConverter("user", class="IpeerUserBundle:User")
     *
     * @Rest\View(statusCode=204)
     *
     * @ApiDoc()
     * @Route("/{id}/users/{user}", name="faculty_user_add")
     * @Method("POST")
     */
    public function addUserAction(Faculty $faculty, User $user)
    {
        $faculty->addUser($user);
        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * Removes a user from the faculty.
     *
     * @param Faculty $faculty
     * @ParamConverter("faculty", class="IpeerCourseBundle:Faculty")
     *
     * @param User $user
     * @ParamConverter("user", class="IpeerUserBundle:User")
     *
     * @Rest\View(statusCode=204)
     *
     * @ApiDoc()
     * @Route("/{id}/users/{user}", name="faculty_user_delete")
     * @Method("DELETE")
     */
    public function removeUserAction(Faculty $faculty, User $user)
    {
        if($user->getFaculties()->contains($faculty)) {
            $faculty->removeUser($user);
            $this->getDoctrine()->getManager()->flush();
        } else {
            throw new NotFoundHttpException();
        }

    }

}
