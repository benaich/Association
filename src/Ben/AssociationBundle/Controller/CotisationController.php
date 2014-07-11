<?php

namespace Ben\AssociationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Ben\AssociationBundle\Entity\Cotisation;
use Ben\AssociationBundle\Form\CotisationType;
use Ben\UserBundle\Entity\User;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Cotisation controller.
 *
 */
class CotisationController extends Controller
{
    /**
     * Lists all Cotisation entities.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BenAssociationBundle:Cotisation')->findAll();

        return $this->render('BenAssociationBundle:Cotisation:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Cotisation entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:Cotisation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cotisation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BenAssociationBundle:Cotisation:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Cotisation entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function newAction(User $user)
    {
        $entity = new Cotisation();
        $entity->setUser($user);
        $form   = $this->createForm(new CotisationType(), $entity);

        return $this->render('BenAssociationBundle:Cotisation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Cotisation entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Cotisation();
        $form = $this->createForm(new CotisationType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cotisation_show', array('id' => $entity->getId())));
        }

        return $this->render('BenAssociationBundle:Cotisation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Cotisation entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:Cotisation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cotisation entity.');
        }

        $editForm = $this->createForm(new CotisationType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BenAssociationBundle:Cotisation:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Cotisation entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:Cotisation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cotisation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CotisationType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cotisation_edit', array('id' => $id)));
        }

        return $this->render('BenAssociationBundle:Cotisation:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Cotisation entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BenAssociationBundle:Cotisation')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Cotisation entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('cotisation'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
