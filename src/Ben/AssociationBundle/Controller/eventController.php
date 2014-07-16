<?php

namespace Ben\AssociationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Ben\AssociationBundle\Entity\event;
use Ben\AssociationBundle\Form\eventType;

use Ben\AssociationBundle\Pagination\Paginator;

/**
 * event controller.
 *
 */
class eventController extends Controller
{
    /**
     * Lists all event entities.
     * @Secure(roles="ROLE_ADMIN")
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
     * @Secure(roles="ROLE_ADMIN")
     *
     */
    public function ajaxListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $searchParam = $request->get('searchParam');

        $entities = $em->getRepository('BenAssociationBundle:event')->search($searchParam);
        $pagination = (new Paginator())->setItems(count($entities), $searchParam['perPage'])->setPage($searchParam['page'])->toArray();
        return $this->render('BenAssociationBundle:Event:ajax_list.html.twig', array(
                    'entities' => $entities,
                    'pagination' => $pagination,
                    ));
    }

    /**
     * Finds and displays a event entity.
     * @Secure(roles="ROLE_USER")
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
     * @Secure(roles="ROLE_ADMIN")
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
     * @Secure(roles="ROLE_ADMIN")
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new event();
        $form = $this->createForm(new eventType(), $entity);
        $form->bind($request);
        $jsonEnabled = $request->get('json');

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            if($jsonEnabled)
                return $this->redirect($this->generateUrl('event_show_json', array('id' => $entity->getId())));                
            return $this->redirect($this->generateUrl('event_show', array('id' => $entity->getId())));
        }
        if($jsonEnabled)
            return $this->render('BenAssociationBundle:event:new_json.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),
            ));
        return $this->render('BenAssociationBundle:event:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing event entity.
     * @Secure(roles="ROLE_ADMIN")
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
     * @Secure(roles="ROLE_ADMIN")
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
     * @Secure(roles="ROLE_ADMIN")
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
     * @Secure(roles="ROLE_ADMIN")
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
     * @Secure(roles="ROLE_USER")
     *
     */
    public function calendarAction()
    {
        $em = $this->getDoctrine()->getManager();
        $security = $this->container->get('security.context');
        if($security->isGranted('ROLE_ADMIN')){
            $entity = new event();
            $form   = $this->createForm(new eventType(), $entity);
            $entities = $em->getRepository('BenAssociationBundle:event')->search();
            return $this->render('BenAssociationBundle:event:calendar.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
            ));
        }
        $user = $security->getToken()->getUser();
        $entities = $em->getRepository('BenAssociationBundle:event')->search(array('user'=>$user->getId()));
        return $this->render('BenAssociationBundle:event:userCalendar.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * displays a demo letters.
     * @Secure(roles="ROLE_ADMIN")
     *
     */
    public function demoAction(Event $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('BenUserBundle:User')->getUsersByEvent($entity->getId());

        return $this->render('BenAssociationBundle:event:demo.html.twig', array(
            'entity'      => $entity,
            'users'      => $users,
            ));
    }

    /**
     * send mails.
     * @Secure(roles="ROLE_ADMIN")
     *
     */
    public function sendAction(Event $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('BenUserBundle:User')->getUsersByEvent($entity->getId(), true);
        // var_dump($users);die();
        $sender_user = $this->container->get('fos_user.user_manager')->findUserByUsername('admin');
        $sender_email = $em->getRepository('BenAssociationBundle:config')->findOneBy(array('the_key' => 'org_email'))->getTheValue();
        foreach ($users as $user) {
            if($user != $sender_user){
                $body = $this->renderView('BenAssociationBundle:event:email.html.twig', array('user' => $user, 'entity' => $entity));  

                // send message using fos_message
                /*
                $threadBuilder = $this->container->get('fos_message.composer')->newThread();
                $threadBuilder
                    ->addRecipient($user)
                    ->setSender($sender_user)
                    ->setSubject($entity->getName())
                    ->setBody($body);
                $sender = $this->container->get('fos_message.sender');
                $sender->send($threadBuilder->getMessage());
                */

                // send message using swiftmailer
                $message = \Swift_Message::newInstance()
                        ->setSubject($entity->getName())
                        ->setFrom($sender_email)
                        ->setTo($user->getEmail())
                        ->setBody($body, 'text/html');
                $this->get('mailer')->send($message);
            }
        }

        $this->get('session')->getFlashBag()->add('success', "messages envoyées avec succée.");
        return $this->redirect($this->generateUrl('event_publish', array('id' => $entity->getId())));
    }

    /**
     * export letters to pdf
     * @Secure(roles="ROLE_USER")
     */
    public function printAction(Event $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('BenUserBundle:User')->getUsersByEvent($entity->getId());
        // return $this->render('BenAssociationBundle:event:print.html.twig', array('entity'=>$entity,'users'=>$users));

        $now = new \DateTime;
        $now = $now->format('d-m-Y_H-i');
        $html = $this->renderView('BenAssociationBundle:event:print.html.twig', array(
            'entity'      => $entity,
            'users'      => $users,
            ));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="letters'.$now.'.pdf"'
            )
        );
    }

    /**
     * json entity.
     * @Secure(roles="ROLE_ADMIN")
     *
     */
    public function showJsonAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BenAssociationBundle:event')->findOne($id);
        $response = new Response(json_encode($entity->toArray()));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Edits an existing event entity.
     * @Secure(roles="ROLE_ADMIN")
     *
     */
    public function updateDateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:event')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find event entity.');
        }
        $event = new event();
        $form = $this->createForm(new eventType(), $event);
        $form->bind($request);
        $entity->setDateFrom($event->getDateFrom());
        $entity->setDateTo($event->getDateTo());
        $em->persist($entity);
        $em->flush();
        return new Response('1');
    }
}
