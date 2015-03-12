<?php

namespace Ipeer\UserBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ipeer\UserBundle\Entity\User as User;
use Symfony\Component\HttpFoundation\Response;

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
     *  resource=true
     * )
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
     * @return User
     *
     * @ApiDoc()
     *
     * @ParamConverter("user", converter="fos_rest.request_body")
     *
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
     * @return User
     *
     * @ApiDoc()
     *
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
     * @param User $user
     * @param integer $id
     * @return array
     *
     * @ApiDoc()
     *
     * @ParamConverter("user", converter="fos_rest.request_body")
     *
     * @Route("/{id}", name="user_update")
     * @Method("POST")
     */
    public function updateAction(User $user, $id)
    {
        $user->setId($id);

        $em = $this->getDoctrine()->getManager();
        $em->merge($user);

        $em->flush();

        return array(
            'user' => $user,
        );
    }
    /**
     * Deletes a User entity.
     *
     * @param User $user
     * @return Response
     *
     * @ApiDoc()
     *
     * @Route("/{id}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return (new Response())->setStatusCode(204);
    }
}
