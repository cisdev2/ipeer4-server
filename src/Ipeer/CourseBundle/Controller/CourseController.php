<?php

namespace Ipeer\CourseBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ipeer\CourseBundle\Entity\Course;
use Symfony\Component\HttpFoundation\Response;

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
     *  resource=true
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
     * @ApiDoc()
     *
     * @Route("", name="course_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {

        $course = $this->get('jms_serializer')->deserialize($request->getContent(),'Ipeer\CourseBundle\Entity\Course', 'json');

        if($course instanceof Course === false) {
            return new Response($course,'400');
        }

        $errors = $this->get('validator')->validate($course);

        if(count($errors) > 0) {
            return new Response((string) $errors, '400');
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($course);
        $em->flush();

        return $course;
    }

    /**
     * Creates a form to create a Course entity.
     *
     * @param Course $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Course $entity)
    {
        $form = $this->createForm(new CourseType(), $entity, array(
            'action' => $this->generateUrl('course_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Course entity.
     *
     * @Route("/new", name="course_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Course();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Course entity.
     *
     * @Route("/{id}", name="course_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IpeerCourseBundle:Course')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Course entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Course entity.
     *
     * @Route("/{id}/edit", name="course_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IpeerCourseBundle:Course')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Course entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Course entity.
    *
    * @param Course $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Course $entity)
    {
        $form = $this->createForm(new CourseType(), $entity, array(
            'action' => $this->generateUrl('course_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Course entity.
     *
     * @Route("/{id}", name="course_update")
     * @Method("PUT")
     * @Template("UBCiPeerCourseBundle:Course:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('IpeerCourseBundle:Course')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Course entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('course_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Course entity.
     *
     * @Route("/{id}", name="course_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('IpeerCourseBundle:Course')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Course entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('course'));
    }

    /**
     * Creates a form to delete a Course entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('course_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
