<?php

namespace Ben\AssociationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Ben\AssociationBundle\Entity\Cotisation;
use Ben\AssociationBundle\Form\CotisationType;
use Ben\UserBundle\Entity\User;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Ben\AssociationBundle\Pagination\Paginator;

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
        $groups = $em->getRepository('BenUserBundle:Group')->findAll();
        $entitiesLength = $em->getRepository('BenAssociationBundle:Cotisation')->counter();
        return $this->render('BenAssociationBundle:Cotisation:index.html.twig', array(
                'groups' => $groups,
                'entitiesLength' => $entitiesLength));
    }

    /**
     * ajax Lists Cotisation entities.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function ajaxListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $searchParam = $request->get('searchParam');

        $entities = $em->getRepository('BenAssociationBundle:Cotisation')->search($searchParam);
        $pagination = (new Paginator())->setItems(count($entities), $searchParam['perPage'])->setPage($searchParam['page'])->toArray();
        return $this->render('BenAssociationBundle:Cotisation:ajax_list.html.twig', array(
                    'entities' => $entities,
                    'pagination' => $pagination,
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
    public function newAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Cotisation();
        $user = $em->getRepository('BenUserBundle:User')->findUser($id);
        $entity->setUser($user);
        $entity->setPrice($user->getProfile()->getMontant());
        $entity->setType($user->getProfile()->getMethod());
        $form   = $this->createForm(new CotisationType(), $entity);
        $cotisations = $user->getCotisations();
        $total = 0;
        foreach ($cotisations as $item) {
            $total += $item->getPrice();
        }
        $daysleft = $em->getRepository('BenAssociationBundle:Cotisation')->daysleft($id);
        $daysleft['user'] = $id;

        return $this->render('BenAssociationBundle:Cotisation:new.html.twig', array(
            'entity' => $entity,
            'total' => $total,
            'form'   => $form->createView(),
            'cotisations' => $cotisations,
            'daysleft' => $daysleft,
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
            return new Response('1');
            // $this->get('session')->getFlashBag()->add('success', "ben.flash.success.contribution.created");
            // return $this->redirect($this->generateUrl('cotisation_show', array('id' => $entity->getId())));
        }

        return $this->render('BenAssociationBundle:Cotisation:form.html.twig', array(
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

            $this->get('session')->getFlashBag()->add('success', "ben.flash.success.contribution.updated");
            return $this->redirect($this->generateUrl('cotisation_edit', array('id' => $id)));
        }

        $this->get('session')->getFlashBag()->add('success', "ben.flash.error.form");
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

        $this->get('session')->getFlashBag()->add('success', "ben.flash.success.contribution.deleted");
        return $this->redirect($this->generateUrl('cotisation_new', array('id' => $entity->getUser()->getId())));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
