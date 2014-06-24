<?php

namespace Ben\AssociationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Ben\AssociationBundle\Entity\Hotels;
use Ben\AssociationBundle\Form\HotelsType;

/**
 * Hotels controller.
 *
 */
class HotelsController extends Controller
{
    /**
     * Lists all Hotels entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $keyword = $request->get('keyword');
        if(isset($keyword) && !empty($keyword)) {
            $entities = $em->getRepository('BenAssociationBundle:Hotels')->findByName($keyword);
        }
        else{
            $entities = $em->getRepository('BenAssociationBundle:Hotels')->findAll();
        }
        

        return $this->render('BenAssociationBundle:Hotels:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Hotels entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:Hotels')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Hotels entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BenAssociationBundle:Hotels:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Hotels entity.
     *
     */
    public function newAction()
    {
        $entity = new Hotels();
        $form   = $this->createForm(new HotelsType(), $entity);

        return $this->render('BenAssociationBundle:Hotels:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Hotels entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Hotels();
        $form = $this->createForm(new HotelsType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('hotels_show', array('id' => $entity->getId())));
        }

        return $this->render('BenAssociationBundle:Hotels:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Hotels entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:Hotels')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Hotels entity.');
        }

        $editForm = $this->createForm(new HotelsType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BenAssociationBundle:Hotels:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Hotels entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:Hotels')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Hotels entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new HotelsType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('hotels_edit', array('id' => $id)));
        }

        return $this->render('BenAssociationBundle:Hotels:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Hotels entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BenAssociationBundle:Hotels')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Hotels entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('hotels'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
