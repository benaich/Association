<?php

namespace Ben\AssociationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Ben\AssociationBundle\Entity\Reservation;
use Ben\AssociationBundle\Form\ReservationType;
use Ben\UserBundle\Entity\User;

use Ben\AssociationBundle\Pagination\Paginator;

/**
 * Reservation controller.
 *
 */
class ReservationController extends Controller
{
    /**
     * Lists all Reservation entities.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $groups = $em->getRepository('BenUserBundle:Group')->findAll();
        $entitiesLength = $em->getRepository('BenAssociationBundle:event')->counter();
        return $this->render('BenAssociationBundle:Reservation:index.html.twig', array(
                'groups' => $groups,
                'entitiesLength' => $entitiesLength[1]));
    }

    public function ajaxListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $perPage = $request->get('perpage');
        $page = $request->get('page');
        $keyword = $request->get('keyword');
        $group = $request->get('group');

        $dateFrom = $request->get('date-from');
        $dateTo = $request->get('date-to');
        $dateFrom = (empty($dateFrom)) ? null : new \DateTime($dateFrom);
        $dateTo = (empty($dateTo)) ? null : new \DateTime($dateTo);

        $entities = $em->getRepository('BenAssociationBundle:Reservation')->findSome($perPage, $page, $keyword, $group, $dateFrom, $dateTo);
        $pagination = (new Paginator())->setItems(count($entities), $perPage)->setPage($page)->toArray();
        return $this->render('BenAssociationBundle:Reservation:ajax_list.html.twig', array(
                    'entities' => $entities,
                    'pagination' => $pagination,
                    ));
    }
    /**
     * Finds and displays a Reservation entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:Reservation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Reservation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BenAssociationBundle:Reservation:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Reservation entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function newAction(User $user)
    {
        if ($user->hasReservation()) {
            $username = $user->getUsername();
            $this->get('session')->getFlashBag()->add('error', "L'utilisateur  $username a déja fait une reservation");
            return $this->redirect($this->generateUrl('ben_show_user', array('id' => $user->getId())));
        }
            
        $entity = new Reservation();
        $entity->setUser($user);
        $form   = $this->createForm(new ReservationType(), $entity);

        return $this->render('BenAssociationBundle:Reservation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Reservation entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Reservation();
        $form = $this->createForm(new ReservationType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $entity->getRoom()->minusFree();
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "Reservation effectué avec succée.");
            return $this->redirect($this->generateUrl('reservation_show', array('id' => $entity->getId())));
        }

        return $this->render('BenAssociationBundle:Reservation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Reservation entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function editAction(Reservation $entity)
    {
        $entity->setOldroom($entity->getRoom()->getId());

        $editForm = $this->createForm(new ReservationType(), $entity);
        $deleteForm = $this->createDeleteForm($entity->getId());

        return $this->render('BenAssociationBundle:Reservation:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Reservation entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function updateAction(Request $request, Reservation $entity)
    {
        $em = $this->getDoctrine()->getManager();

        $deleteForm = $this->createDeleteForm($entity->getId());
        $editForm = $this->createForm(new ReservationType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            if ($entity->getOldroom() != $entity->getRoom()->getId()) {
                $room = $em->getRepository('BenAssociationBundle:Rooms')->find($entity->getOldroom());
                $room->plusFree();
                $entity->getRoom()->minusFree();
                $em->persist($room);
            }
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "Action effectué avec succée.");
            return $this->redirect($this->generateUrl('reservation_edit', array('id' => $entity->getId())));
        }

        return $this->render('BenAssociationBundle:Reservation:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
 
    /**
     * Deletes a Reservation entities.
     * @Secure(roles="ROLE_MANAGER")
     *
     */   
    
    public function removeEntitiesAction(Request $request)
    {
        $entities = $request->get('entities');
        $em = $this->getDoctrine()->getManager();
        foreach( $entities as $id){
            $entity = $em->getRepository('BenAssociationBundle:Reservation')->find($id);
            $em->remove($entity);
        }
        $em->flush();
        return new Response('supression effectué avec succès');
    } 

    /**
     * Deletes a Reservation entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BenAssociationBundle:Reservation')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Reservation entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('reservation'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
