<?php

namespace Ben\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;


use Ben\UserBundle\Entity\User;
use Ben\UserBundle\Entity\Group;
use Ben\UserBundle\Form\GroupType;
use Ben\AssociationBundle\Pagination\Paginator;

/**
 * Group controller.
 *
 */
class GroupController extends Controller
{
    /**
     * Lists all Group entities.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BenUserBundle:Group')->findAll();
        $entity = new Group();
        $form   = $this->createForm(new GroupType(), $entity);

        return $this->render('BenUserBundle:Group:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Group entity.
     * @Secure(roles="ROLE_MANAGER")
     */
    public function showAction(Request $request, Group $group, $perPage)
    {
        $em = $this->getDoctrine()->getManager();
        if($request->getMethod()==='POST') $searchParam = $request->get('searchParam');
        else $searchParam['page'] = 1;
        $searchParam['perPage'] = $perPage;
        $searchParam['group'] = $group->getId();
        $entities = $em->getRepository('BenUserBundle:user')->search($searchParam);
        $pagination = (new Paginator())->setItems(count($entities), $searchParam['perPage'])->setPage($searchParam['page'])->toArray();
        return $this->render('BenUserBundle:Group:show.html.twig', array(
                    'group' => $group,
                    'entities' => $entities,
                    'pagination' => $pagination,
                    ));
    }

    /**
     * Creates a new Group entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Group();
        $form = $this->createForm(new GroupType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->getImage()->upload();
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'ben.flash.success.group.created');
            return $this->redirect($this->generateUrl('group'));
        }
        
        $this->get('session')->getFlashBag()->add('danger', 'ben.flash.error.form');
        $entities = $em->getRepository('BenUserBundle:Group')->findAll();
        return $this->render('BenUserBundle:Group:index.html.twig', array(
            'entities' => $entities,
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Group entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function editAction(Group $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createForm(new GroupType(), $entity);

        return $this->render('BenUserBundle:Group:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Edits an existing Group entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function updateAction(Request $request, Group $entity)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm(new GroupType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'ben.flash.success.group.updated');
            return $this->redirect($this->generateUrl('group_edit', array('id' => $entity->getId())));
        }
        else $this->get('session')->getFlashBag()->add('error', 'ben.flash.error.form');

        return $this->render('BenUserBundle:Group:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Group entity.
     * @Secure(roles="ROLE_ADMIN")
     *
     */
    public function deleteAction(Request $request, Group $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', 'ben.flash.success.group.deleted');
        return $this->redirect($this->generateUrl('group'));
    }


    /**
     * ajouter un groupe de recherche
     * @Secure(roles="ROLE_MANAGER")
     */
    public function addFilterGroupAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $searchParam = $request->get('searchParam');
        $entities = $em->getRepository('BenUserBundle:user')->search($searchParam);
        $blackConstraint = new \Symfony\Component\Validator\Constraints\NotBlank();
        $errorList = $this->get('validator')->validateValue($searchParam['filterGroup'], $blackConstraint);

        if (count($errorList) == 0) {
            $group = new Group();
            $group->setName($searchParam['filterGroup']);
            $group->setRoles(array());
            foreach ($entities as $entity) {
                $entity->groupAdd($group);
            }
            $em->persist($group);
            $em->flush();
        $response = new Response(json_encode($group->toArray()));
        $response->headers->set('Content-Type', 'application/json');
        } else {
            $errorMessage = $errorList[0]->getMessage();
            $response = new Response($errorMessage);
        }

        return $response;
    }

    /**
     * associer les adhérants sélectionnés à un groupe
     * @Secure(roles="ROLE_MANAGER")
     */
    public function addToGroupAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $request->get('users');
        $group_id = $request->get('group');
        $group = $em->getRepository('BenUserBundle:Group')->find($group_id);
        $userManager = $this->get('fos_user.user_manager');
        foreach( $users as $id){
            $user = $userManager->findUserBy(array('id' => $id));
            $user->groupAdd($group);
        }
        $em->persist($group);
        $em->flush();

        $response = new Response(json_encode($group->toArray()));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * supprimer un utilisateur d'un groupe
     * @Secure(roles="ROLE_MANAGER")
     */
    public function removeFromGroupAction(Request $request, User $user, $groupid)
    {
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('BenUserBundle:Group')->find($groupid);
        $user->groupRemove($group);
        $em->persist($group);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', "ben.flash.success.general");
        return $this->redirect($this->generateUrl('group_show', array('id' => $groupid)));
    }

    /**
     * log mail, sms, call fo a group
     * @Secure(roles="ROLE_MANAGER")
     */
    public function logGroupAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('BenUserBundle:user')->search(array('group'=>$id));
        foreach ($users as $user) {
            $entity = $this->getLog($request->get('log'));
            $entity->setEntityId($user->getId());
            $em->persist($entity);
        }
        $em->flush();
        return new Response('1');
    }

    /**
     * send mail
     * @Secure(roles="ROLE_MANAGER")
     */
    public function sendMailGroupAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $mail = $request->get('mail');
        if($id == 0){ //send mail to one person
            $recipients = $mail['email'];
            $logEntity = $this->getLog($request->get('log'));
            $logEntity->setMessage($mail['subject']);
            $logEntity->setType('mail');
            $em->persist($logEntity);
        }
        else{ //send mail to a group
            $users = $em->getRepository('BenUserBundle:user')->search(array('group'=>$id));
            $recipients = [];
            foreach ($users as $user) {
                $recipients[] = $user->getEmail();
                $logEntity = $this->getLog($request->get('log'));
                $logEntity->setMessage($mail['subject']);
                $logEntity->setType('mail');
                $logEntity->setEntityId($user->getId());
                $em->persist($logEntity);
            }
        }
        $this->sendMail($mail, $recipients);
        $em->flush();
        return new Response('1');
    }

    /**
     * send mail
     * @Secure(roles="ROLE_MANAGER")
     */
    public function sendLetterAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('BenUserBundle:user')->search(array('group'=>$id));
        
        $users = $em->getRepository('BenUserBundle:user')->search(array('group'=>$id));
        if(empty($users)) {
            $this->get('session')->getFlashBag()->add('danger', "ben.flash.error.group.empty");
            return $this->redirect($this->generateUrl('group_show', array('id' => $id)));
        }
        // return $this->render('BenUserBundle:Group:letter.html.twig', array('letter'=>$letter,'users'=>$users));

        // log users who receved letters
        $letter = $request->get('letter');
        $log = $request->get('log');
        foreach ($users as $user) {
            $log['entity_id'] = $user->getId();
            $em->persist($this->getLog($log));
        }
        $em->flush();

        $now = (new \DateTime)->format('d-m-Y_H-i');
        $html = $this->renderView('BenUserBundle:Group:letter.html.twig', array(
            'letter'      => $letter,
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

    /* helper funcions */
    private function getLog($log)
    {
        extract($log);
        if(!empty($sms)){
            $type = 'sms';
            $feedback = '';
        }
        $entity = new \Ben\AssociationBundle\Entity\ActivityLog();
        $entity->setClassName('Ben\UserBundle\Entity\User');
        $entity->setEntityId($entity_id);
        $entity->setUser($user);
        $entity->setMessage($feedback);
        $entity->setType($type);
        return $entity;
    }

    public function sendMail($mail, $recipients)
    {
        extract($mail);
        $sender_email = $this->container->getParameter('webmaster');
        $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom($sender_email)
                    ->setTo($recipients)
                    ->setBody($body, 'text/plain');
        $this->get('mailer')->send($message);
    }
}
