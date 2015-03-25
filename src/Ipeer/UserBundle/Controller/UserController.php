<?php

namespace Ipeer\UserBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ipeer\UserBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * User controller.
 *
 * @Route("/users")
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     *
     * @ApiDoc(
     *  resource=true,
     * )
     *
     * @return User[]
     *
     * @Route("", name="user")
     * @Method("GET")
     */
    public function indexAction()
    {
        return array(
            'users' => $this->getDoctrine()->getRepository('IpeerUserBundle:User')->findAll(),
        );
    }

    /**
     * Creates a new User entity.
     *
     * @param User $user
     * @ParamConverter("user", converter="fos_rest.request_body")
     *
     * @return User
     *
     * @ApiDoc()
     * @Route("", name="user_create")
     * @Method("POST")
     */
    public function createAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * Finds and displays a User entity.
     *
     * @param User $user
     *
     * @return User
     *
     * @ApiDoc()
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(User $user)
    {
        return $user;
    }

    /**
     * Edits an existing User entity.
     *
     * @param User $user The data the user submitted
     * @ParamConverter("user", converter="fos_rest.request_body")
     *
     * @param User $id The entity to be updated
     * @ParamConverter("id", class="IpeerUserBundle:User")
     *
     * @return array
     *
     * @ApiDoc()
     * @Route("/{id}", name="user_update")
     * @Method("POST")
     */
    public function updateAction(User $id, User $user)
    {
        // inject the id value from the URL
        // (ensures update instead of creating a new duplicate)
        $user->setId($id->getId());

        $em = $this->getDoctrine()->getManager();
        $em->merge($user);
        $em->flush();

        return $user;
    }

    /**
     * Deletes a User entity.
     *
     * @param User $user
     *
     * @Rest\View(statusCode=204)
     *
     * @ApiDoc()
     * @Route("/{id}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
    }
}
