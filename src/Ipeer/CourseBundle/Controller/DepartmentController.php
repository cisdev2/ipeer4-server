<?php

namespace Ipeer\CourseBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Ipeer\CourseBundle\Entity\Department;

/**
 * Department controller.
 *
 * @Route("/faculties/{faculty}/department")
 */
class DepartmentController extends Controller
{

    /**
     * Finds and displays a Department entity.
     *
     * @Route("/{id}", name="department_show")
     * @Method("GET")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IpeerCourseBundle:Department')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Department entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }
}
