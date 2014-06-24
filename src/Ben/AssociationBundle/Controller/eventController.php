<?php

namespace Ben\AssociationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Ben\AssociationBundle\Entity\event;
use Ben\AssociationBundle\Form\eventType;
use DateTime;

/**
 * event controller.
 *
 */
class eventController extends Controller
{
    /**
     * Lists all event entities.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $groups = $em->getRepository('BenUserBundle:Group')->findAll();
        $entitiesLength = $em->getRepository('BenAssociationBundle:event')->counter();
        return $this->render('BenAssociationBundle:event:index.html.twig', array(
                'groups' => $groups,
                'entitiesLength' => $entitiesLength[1]));
    }

    /**
     * ajax Lists event entities.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
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

        $template='BenAssociationBundle:event:ajax_list.html.twig';
        $entities = $em->getRepository('BenAssociationBundle:event')->findSome($perPage, $page, $keyword, $group, $dateFrom, $dateTo);
        return $this->render($template, array(
                    'entities' => $entities,
                    'nombreParPage' => $perPage,
                    'nombrePage' => ceil(count($entities) / $perPage),
                    'page' => $page));
    }

    /**
     * Finds and displays a event entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find event entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BenAssociationBundle:event:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new event entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function newAction()
    {
        $entity = new event();
        $form   = $this->createForm(new eventType(), $entity);

        return $this->render('BenAssociationBundle:event:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new event entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new event();
        $form = $this->createForm(new eventType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('event_show', array('id' => $entity->getId())));
        }

        return $this->render('BenAssociationBundle:event:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing event entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find event entity.');
        }

        $editForm = $this->createForm(new eventType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BenAssociationBundle:event:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing event entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:event')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find event entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new eventType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('event_edit', array('id' => $id)));
        }

        return $this->render('BenAssociationBundle:event:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a event entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BenAssociationBundle:event')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find event entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('event'));
    }

    
    /**
     * Deletes a event entities.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function removeEntitiesAction(Request $request)
    {
        $entities = $request->get('entities');
        $em = $this->getDoctrine()->getManager();
        foreach( $entities as $id){
            $entity = $em->getRepository('BenAssociationBundle:event')->find($id);
            $em->remove($entity);
        }
        $em->flush();
        return new Response('supression effectué avec succès');
    } 


    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * show the calendar
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function calendarAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('BenAssociationBundle:event')->findAll();
        return $this->render('BenAssociationBundle:event:calendar.html.twig', array('entities' => $entities));
    }
}
