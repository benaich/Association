<?php

namespace Ben\AssociationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Ben\AssociationBundle\Entity\Avancement;
use Ben\AssociationBundle\Form\AvancementType;
use Ben\UserBundle\Entity\User;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Avancement controller.
 *
 */
class AvancementController extends Controller
{
    /**
     * Displays a form to create a new Avancement entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function newAction(User $user)
    {
        $entity = new Avancement();
        $entity->setUser($user);
        $form   = $this->createForm(new AvancementType(), $entity);

        return $this->render('BenAssociationBundle:Avancement:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Avancement entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Avancement();
        $form = $this->createForm(new AvancementType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "ben.flash.success.status.created");
            return $this->redirect($this->generateUrl('ben_show_user', array('id' => $entity->getUser()->getId())));
        }

        return $this->render('BenAssociationBundle:Avancement:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Avancement entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:Avancement')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Avancement entity.');
        }

        $editForm = $this->createForm(new AvancementType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BenAssociationBundle:Avancement:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Avancement entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:Avancement')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Avancement entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new AvancementType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "ben.flash.success.status.updated");
            return $this->redirect($this->generateUrl('ben_show_user', array('id' => $entity->getUser()->getId())));
        }

        return $this->render('BenAssociationBundle:Avancement:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Avancement entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BenAssociationBundle:Avancement')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Avancement entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

            $this->get('session')->getFlashBag()->add('success', "ben.flash.success.status.deleted");
            return $this->redirect($this->generateUrl('ben_show_user', array('id' => $entity->getUser()->getId())));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
