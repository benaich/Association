<?php

namespace Ben\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Ben\UserBundle\Entity\User;
use Ben\UserBundle\Form\userType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Ben\UserBundle\Form\profileType;
use Ben\UserBundle\Entity\Group;

use Ben\AssociationBundle\Pagination\Paginator;

class AdminController extends Controller
{
    /**
     * la page des adhérants
     * @Secure(roles="ROLE_MANAGER")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $groups = $em->getRepository('BenUserBundle:Group')->findAll();
        $status = $em->getRepository('BenAssociationBundle:Status')->findAll();
        $entitiesLength = $em->getRepository('BenUserBundle:User')->counter();
        return $this->render('BenUserBundle:admin:index.html.twig', array(
                'groups' => $groups,
                'status' => $status,
                'entitiesLength' => $entitiesLength));
    }

    /**
     * liste des adhérants avec ajax
     * @Secure(roles="ROLE_MANAGER")
     */
    public function ajaxListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $searchParam = $request->get('searchParam');
        $template='BenUserBundle:admin:ajax_list.html.twig';
        $entities = $em->getRepository('BenUserBundle:user')->search($searchParam);
        $pagination = (new Paginator())->setItems(count($entities), $searchParam['perPage'])->setPage($searchParam['page'])->toArray();
        return $this->render($template, array(
                    'entities' => $entities,
                    'pagination' => $pagination,
                    ));
    }

    /**
     * formulaire d'ajout d'un adhérant
     * @Secure(roles="ROLE_MANAGER")
     */
    public function newAction()
    {
        $config = $this->getConfig();
        $entity = new User();
        $form = $this->createForm(new userType($config), $entity);
        return $this->render('BenUserBundle:admin:new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

    /**
     * ajouter un adhérant
     * @Secure(roles="ROLE_MANAGER")
     */
    public function addAction(Request $request)
    {
        $em = $this->get('fos_user.user_manager');
        $config = $this->getConfig();
        $entity = new User();
        $form = $this->createForm(new userType($config), $entity);
        $form->bind($request);
        if ($form->isValid()) {
            $em->updateUser($entity, false);
            $entity->getProfile()->getImage()->upload();
            $entity->addGroup($this->container->get('fos_user.group_manager')->findGroupByName('Adhérents'));

            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', "ben.flash.success.user.created");
            return $this->redirect($this->generateUrl('ben_show_user', array('id' => $entity->getId())));
        }
        $this->get('session')->getFlashBag()->add('error', "ben.flash.error.form");

        return $this->render('BenUserBundle:admin:new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

    /**
     * afficher un adhérant
     * @Secure(roles="ROLE_USER")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $security = $this->container->get('security.context');
        if(!$security->isGranted('ROLE_MANAGER'))
            $id = $security->getToken()->getUser()->getId();
        $entity = $em->getRepository('BenUserBundle:user')->findUser($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find posts entity.');
        }

        $logs = $em->getRepository('BenAssociationBundle:ActivityLog')->findBy(array('entity_id' => $id));
        $deleteForm = $this->createDeleteForm($id);
        return $this->render('BenUserBundle:admin:show.html.twig', array(
            'entity' => $entity,
            'logs' => $logs,
            'delete_form' => $deleteForm->createView(),
            ));
    }

    /**
     * formulaire de modification d'un adhérant
     * @Secure(roles="ROLE_MANAGER")
     */
    public function editAction(User $user)
    {
        /* check if user has admin role */
        if (in_array('ROLE_ADMIN', $user->getRoles()) !== false ){
            $this->get('session')->getFlashBag()->add('error', "ben.flash.error.user.admin");
            return $this->redirect($this->generateUrl('ben_users'));
        }
        $config = $this->getConfig();
        $form = $this->createForm(new userType($config), $user);
        return $this->render('BenUserBundle:admin:edit.html.twig', array('entity' => $user, 'form' => $form->createView()));
    }

    /**
     * mettre à jour un adhérant
     * @Secure(roles="ROLE_MANAGER")
     */
    public function updateAction(Request $request, User $user) {
        $em = $this->get('fos_user.user_manager');
        $config = $this->getConfig();
        $form = $this->createForm(new userType($config), $user);
        $form->bind($request);
        /* check if user has admin role */
        if (in_array('ROLE_ADMIN', $user->getRoles()) !== false ){
            $this->get('session')->getFlashBag()->add('error', "ben.flash.error.user.admin");
            return $this->redirect($this->generateUrl('ben_users'));
        }
        if ($form->isValid()) {
            $em->updateUser($user, false);
            $user->getProfile()->getImage()->manualRemove($user->getProfile()->getImage()->getAbsolutePath());
            $user->getProfile()->getImage()->upload();

            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', "ben.flash.success.updated");
            return $this->redirect($this->generateUrl('ben_edit_user', array('id' => $user->getId())));
        }
        $this->get('session')->getFlashBag()->add('error', "ben.flash.error.form");
        
        return $this->render('BenUserBundle:admin:edit.html.twig', array('entity' => $user, 'form' => $form->createView()));
    }

    /**
     * formulaire pour mettre à jour les informations de l'utilisateur connécté
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function editMeAction() {
        $config = $this->getConfig();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $entity = $user->getProfile();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find profile entity.');
        }

        $form = $this->createForm(new profileType($config), $entity);
        return $this->render('BenUserBundle:myProfile:edit.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                ));
    }


    /**
     * mettre à jour les informations de l'utilisateur connécté
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function updateMeAction(Request $request, \Ben\UserBundle\Entity\profile $profile) {
        $em = $this->getDoctrine()->getManager();
        $config = $this->getConfig();
        $form = $this->createForm(new profileType($config), $profile);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($profile);
            $profile->getImage()->manualRemove($profile->getImage()->getAbsolutePath());
            $profile->getImage()->upload();
               
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "ben.flash.success.updated");
            return $this->redirect($this->generateUrl('ben_profile_edit', array('name' => $profile->getId())));
        }
        $this->get('session')->getFlashBag()->add('error', "ben.flash.error.form");

        return $this->render('BenUserBundle:myProfile:edit.html.twig', array(
                    'entity' => $profile,
                    'form' => $form->createView(),
                ));
    }

    /**
     * supprimer un adhérant
     * @Secure(roles="ROLE_ADMIN")
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->findUserBy(array('id' => $id));
            $userManager->deleteUser($user);
        }

        $this->get('session')->getFlashBag()->add('success', "ben.flash.user.deleted");
        return $this->redirect($this->generateUrl('ben_users'));
    }
 
    /**
     * supprimer plusieurs adhérants - ajax
     * @Secure(roles="ROLE_ADMIN")
     */   
    public function removeUsersAction(Request $request)
    {
        $users = $request->get('users');
        $userManager = $this->get('fos_user.user_manager');
        foreach( $users as $id){
            $user = $userManager->findUserBy(array('id' => $id));
            $userManager->deleteUser($user);
        }
        return new Response('supression effectué avec succès');
    } 

    /**
     * activer ou désactiver les adhérants sélectionnés - ajax
     * @Secure(roles="ROLE_MANAGER")
     */
    public function enableUsersAction(Request $request, $etat)
    {
        $users = $request->get('users');
        $userManager = $this->get('fos_user.user_manager');
        $etat = ($etat==1);
        foreach( $users as $id){
            $user = $userManager->findUserBy(array('id' => $id));
            $user->setEnabled($etat);
            $userManager->updateUser($user);
        }
        return new Response('1');
    }

    /**
     * changer le role des adhérants sélectionnés - ajax
     * @Secure(roles="ROLE_ADMIN")
     */    
    public function setRoleAction(Request $request, $role)
    {
        if($role=='admin') $role='ROLE_ADMIN';
        else if($role=='manager') $role='ROLE_MANAGER';
        else $role='ROLE_USER';
        $users = $request->get('users');
        $userManager = $this->get('fos_user.user_manager');
        foreach( $users as $id){
            $user = $userManager->findUserBy(array('id' => $id));
            $user->removeRole('ROLE_MANAGER');
            $user->removeRole('ROLE_ADMIN');
            $user->addRole($role);
            $userManager->updateUser($user);
        }
        return new Response('1');
    }

    /**
     * exporter vers csv
     * @Secure(roles="ROLE_MANAGER")
     */    
    public function toCsvAction()
    {
        $em = $this->getDoctrine()->getEntityManager();        
        $entities = $em->getRepository('BenUserBundle:user')->getUsers();
        $response = $this->render('BenUserBundle:admin:list.csv.twig',array('entities' => $entities));
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="contacts.csv"');
        return $response;
    }

    /**
     * exporter vers xml
     * @Secure(roles="ROLE_MANAGER")
     */    
    public function toXmlAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entities = $em->getRepository('BenUserBundle:user')->getUsers();
        $response = $this->render('BenUserBundle:admin:list.xml.twig',array('entities' => $entities));
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }

    /**
     * exporter vers pdf
     * @Secure(roles="ROLE_USER")
     */
    public function toPdfAction($users)
    {
        $em = $this->getDoctrine()->getManager();

        $security = $this->container->get('security.context');
        if(!$security->isGranted('ROLE_MANAGER'))
            $ids = array($security->getToken()->getUser()->getId());
        elseif($users !== 'all')$ids = explode(',', $users);
        else $ids = null;

        $entities = $em->getRepository('BenUserBundle:user')->search(array('ids'=>$ids));
        // return $this->render('BenUserBundle:admin:badge.html.twig', array('entities' => $entities));

        $now = (new \DateTime)->format('d-m-Y_H-i');
        $html = $this->renderView('BenUserBundle:admin:badge.html.twig', array('entities' => $entities));
        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="carte'.$now.'.pdf"'
            )
        );
    }

    /**
     * générer les tiquets pdf
     * @Secure(roles="ROLE_MANAGER")
     */
    public function printTicketAction($users)
    {
        $em = $this->getDoctrine()->getManager();
        if($users !== 'all')$ids = explode(',', $users);
        else $ids = null;
        $entities = $em->getRepository('BenUserBundle:user')->search(array('ids'=>$ids));
        // return $this->render('BenUserBundle:admin:etiquette.html.twig', array('entities' => $entities));

        $now = (new \DateTime)->format('d-m-Y_H-i');
        $html = $this->renderView('BenUserBundle:admin:etiquette.html.twig', array('entities' => $entities));
        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="etiquette'.$now.'.pdf"'
            )
        );
    }

    /**
     * exporter vers excel
     * @Secure(roles="ROLE_MANAGER")
     */
    public function toExcelAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('BenUserBundle:user')->search(array());
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("ben");
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue("A1", "id")
            ->setCellValue("B1", "CIN")
            ->setCellValue("C1", "nom")
            ->setCellValue("D1", "prenom")
            ->setCellValue("E1", "Identifiant")
            ->setCellValue("F1", "email")
            ->setCellValue("G1", "sexe")
            ->setCellValue("H1", "codebare")
            ->setCellValue("I1", "adresse")
            ->setCellValue("J1", "code postal")
            ->setCellValue("K1", "ville")
            ->setCellValue("L1", "pays")
            ->setCellValue("M1", "tel")
            ->setCellValue("N1", "gsm")
            ->setCellValue("O1", "profession")
            ->setCellValue("P1", "description")
            ->setCellValue("Q1", "diplome")
            ->setCellValue("R1", "expertise")
            ->setCellValue("S1", "status")
            ->setCellValue("T1", "Date de naissance")
            ->setCellValue("U1", "Date d'inscription");
        $i=2;
        foreach ($entities as $entity) {
           $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue("A$i", $entity->getId())
                ->setCellValue("B$i", $entity->getProfile()->getCin())
                ->setCellValue("C$i", $entity->getProfile()->getFamilyName())
                ->setCellValue("D$i", $entity->getProfile()->getFirstName())
                ->setCellValue("E$i", $entity->getUsername())
                ->setCellValue("F$i", $entity->getEmail())
                ->setCellValue("G$i", $entity->getProfile()->getGender())
                ->setCellValue("H$i", $entity->getProfile()->getBarcode())
                ->setCellValue("I$i", $entity->getProfile()->getAddress())
                ->setCellValue("J$i", $entity->getProfile()->getPostCode())
                ->setCellValue("K$i", $entity->getProfile()->getCity())
                ->setCellValue("L$i", $entity->getProfile()->getContry())
                ->setCellValue("M$i", $entity->getProfile()->getTel())
                ->setCellValue("N$i", $entity->getProfile()->getGsm())
                ->setCellValue("O$i", $entity->getProfile()->getJob())
                ->setCellValue("P$i", $entity->getProfile()->getDescription())
                ->setCellValue("Q$i", $entity->getProfile()->getDiplome())
                ->setCellValue("R$i", $entity->getProfile()->getExpertise())
                ->setCellValue("S$i", $entity->getStatus())
                ->setCellValue("T$i", $entity->getProfile()->getBirthday()->format('d/m/Y'))
                ->setCellValue("U$i", $entity->getCreated()->format('d/m/Y'));
            $i++;
       }

        $phpExcelObject->getActiveSheet()->setTitle('Liste des adhérents');
        $phpExcelObject->setActiveSheetIndex(0);
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $now = (new \DateTime)->format('d-m-Y_H-i');
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', "attachment;filename=members-$now.xls");
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;        
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
                $entity->addGroup($group);
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
            $user->addGroup($group);
        }
        $em->persist($group);
        $em->flush();

        $response = new Response(json_encode($group->toArray()));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * liste des utilisateurs d'un groupe
     * @Secure(roles="ROLE_MANAGER")
     */
    public function showGroupAction(Request $request, Group $group, $perPage)
    {
        $em = $this->getDoctrine()->getManager();
        if($request->getMethod()==='POST') $searchParam = $request->get('searchParam');
        else $searchParam['page'] = 1;
        $searchParam['perPage'] = $perPage;
        $searchParam['group'] = $group->getId();
        $entities = $em->getRepository('BenUserBundle:user')->search($searchParam);
        $pagination = (new Paginator())->setItems(count($entities), $searchParam['perPage'])->setPage($searchParam['page'])->toArray();
        return $this->render('BenUserBundle:group:call_list.html.twig', array(
                    'group' => $group,
                    'entities' => $entities,
                    'pagination' => $pagination,
                    ));
    }

    /**
     * supprimer un utilisateur d'un groupe
     * @Secure(roles="ROLE_MANAGER")
     */
    public function removeFromGroupAction(Request $request, User $user, $groupid)
    {
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('BenUserBundle:Group')->find($groupid);
        $user->removeGroup($group);
        $em->persist($group);
        $em->flush();
        $this->get('session')->getFlashBag()->add('success', "ben.flash.success.general");
        return $this->redirect($this->generateUrl('ben_show_group', array('id' => $groupid)));
    }

    /**
     * log mail, sms, call for a user
     * @Secure(roles="ROLE_MANAGER")
     */
    public function logAction(Request $request)
    {
        $entity = $this->getLog($request->get('log'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        return new Response('1');
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
     * liste des utilisateurs public
     * @Secure(roles="ROLE_MANAGER")
     */
    public function publicAction(Request $request, $perPage)
    {
        $em = $this->getDoctrine()->getManager();
        if($request->getMethod()==='POST') $searchParam = $request->get('searchParam');
        else $searchParam['page'] = 1;
        $searchParam['perPage'] = $perPage;
        $entities = $em->getRepository('BenUserBundle:user')->search($searchParam);
        $pagination = (new Paginator())->setItems(count($entities), $searchParam['perPage'])->setPage($searchParam['page'])->toArray();
        return $this->render('BenUserBundle:admin:public.html.twig', array(
                    'entities' => $entities,
                    'pagination' => $pagination,
                    ));
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
     * send mail to selected users
     * @Secure(roles="ROLE_ADMIN")
     */    
    public function sendMailAction(Request $request)
    {
        $ids = $request->get('users');
        $mail = $request->get('mail');
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('BenUserBundle:user')->search(array('ids'=>$ids));
        $recipients = [];
        foreach ($users as $user) {
            $recipients[] = $user->getEmail();
            $logEntity = $this->getLog($request->get('log'));
            $logEntity->setMessage($mail['subject']);
            $logEntity->setEntityId($user->getId());
            $em->persist($logEntity);
        }
        
        if($logEntity->getType()==='mail')
            $this->sendMail($mail, $recipients);
        $em->flush();
        
        return new Response('1');
    }


    /**
     * send mail to selected users
     * @Secure(roles="ROLE_ADMIN")
     */    
    public function clearLogAction(Request $request, $id, $user)
    {
        $em = $this->getDoctrine()->getManager();
        if($id == 0)
            $entities = $em->getRepository('BenAssociationBundle:ActivityLog')->findBy(array('entity_id' => $user));
        else $entities = $em->getRepository('BenAssociationBundle:ActivityLog')->findBy(array('id' => $id));
        foreach ($entities as $entity) $em->remove($entity);
        $em->flush();
        
        return new Response('1');
    }


    /* helper funcions */
    private function getConfig()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('BenAssociationBundle:config')->findAll();
        foreach ($entities as $entity) 
           $config[$entity->getTheKey()] = $entity->getTheValue();
       return $config;
    }
    
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    private function getLog($log)
    {
        extract($log);
        if(!empty($sms)){
            $type = 'sms';
            $feedback = $sms;
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