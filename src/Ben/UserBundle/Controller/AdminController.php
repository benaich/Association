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

use Ben\AssociationBundle\Pagination\Paginator;

class AdminController extends Controller
{
    /**
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
                'entitiesLength' => $entitiesLength[1]));
    }

    /**
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
     * @Secure(roles="ROLE_MANAGER")
     */
    public function newAction()
    {
        $entity = new User();
        $form = $this->createForm(new userType(), $entity);
        return $this->render('BenUserBundle:admin:new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

    /**
     * @Secure(roles="ROLE_MANAGER")
     */
    public function addAction(Request $request)
    {
        $em = $this->get('fos_user.user_manager');
        $entity = new User();
        $form = $this->createForm(new userType(), $entity);
        $form->bind($request);
        if ($form->isValid()) {
            $em->updateUser($entity, false);
            $entity->getProfile()->getImage()->upload();
            $entity->addGroup($this->container->get('fos_user.group_manager')->findGroupByName('Adhérents'));

            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', "Adhérent ajouté avec succée.");
            return $this->redirect($this->generateUrl('ben_show_user', array('id' => $entity->getId())));
        }
        $this->get('session')->getFlashBag()->add('error', "Il y a des erreurs dans le formulaire soumis !");

        return $this->render('BenUserBundle:admin:new.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

    /**
     * @Secure(roles="ROLE_MANAGER")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('BenUserBundle:user')->findUser($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find posts entity.');
        }
        $deleteForm = $this->createDeleteForm($id);
        return $this->render('BenUserBundle:admin:show.html.twig', array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            ));
    }

    /**
     * @Secure(roles="ROLE_MANAGER")
     */
    public function editAction(User $entity)
    {
        $form = $this->createForm(new userType($type), $entity);
        return $this->render('BenUserBundle:admin:edit.html.twig', array('entity' => $entity, 'form' => $form->createView()));
    }

    /**
     * @Secure(roles="ROLE_MANAGER")
     */
    public function updateAction(Request $request, User $user) {
        $em = $this->get('fos_user.user_manager');
        $form = $this->createForm(new userType(), $user);
        $form->bind($request);
        /* check if user has admin role */
        /*if (array_search('ROLE_ADMIN', $user->getRoles()) !== false ){
            $this->get('session')->getFlashBag()->add('Unauthorized access', "impossible de modifier un super utilisateur de cette interface");
            return $this->redirect($this->generateUrl('ben_users'));
        }*/
        if ($form->isValid()) {
            $em->updateUser($user, false);
            $user->getProfile()->getImage()->manualRemove($user->getProfile()->getImage()->getAbsolutePath());
            $user->getProfile()->getImage()->upload();

            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add('success', "Vos modifications ont été enregistrées.");
            return $this->redirect($this->generateUrl('ben_edit_user', array('id' => $user->getId())));
        }
        $this->get('session')->getFlashBag()->add('error', "Il y a des erreurs dans le formulaire soumis !");
        
        return $this->render('BenUserBundle:admin:edit.html.twig', array('entity' => $user, 'form' => $form->createView()));
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
            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->findUserBy(array('id' => $id));
            $userManager->deleteUser($user);
        }

        $this->get('session')->getFlashBag()->add('success', "L'adhérent a été supprimé avec succée.");
        return $this->redirect($this->generateUrl('ben_users'));
    }
 
    /**
     * @Secure(roles="ROLE_MANAGER")
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
     * @Secure(roles="ROLE_MANAGER")
     */    
    public function setRoleAction(Request $request, $role)
    {
        if($role=='admin') $role='ROLE_ADMIN';
        else if($role=='manager') $role='ROLE_MANAGER';
        else if($role=='author') $role='ROLE_AUTHOR';
        else if($role=='premium') $role='ROLE_PREMIUM';
        else $role='ROLE_USER';
        $users = $request->get('users');
        $userManager = $this->get('fos_user.user_manager');
        foreach( $users as $id){
            $user = $userManager->findUserBy(array('id' => $id));
            $user->removeRole('ROLE_MANAGER');
            $user->removeRole('ROLE_ADMIN');
            $user->removeRole('ROLE_AUTHOR');
            $user->removeRole('ROLE_PREMIUM');
            $user->addRole($role);
            $userManager->updateUser($user);
        }
        return new Response('1');
    }

    /**
     * @Secure(roles="ROLE_MANAGER")
     */    
    public function toCsvAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $entities = $em->getRepository('BenUserBundle:user')->getUsers();
        $response = $this->render('BenUserBundle:admin:list.csv.twig',array(
                    'entities' => $entities,
                    ));
         $response->headers->set('Content-Type', 'text/csv');
         $response->headers->set('Content-Disposition', 'attachment; filename="contacts.csv"');

        return $response;
    }


    /**
     * Displays a form to edit an existing profil entity.
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function editMeAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $entity = $user->getProfile();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find profile entity.');
        }

        $form = $this->createForm(new profileType(), $entity);
        return $this->render('BenUserBundle:myProfile:edit.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                ));
    }


    /**
     * Edits an existing profil entity.
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function updateMeAction(Request $request, \Ben\UserBundle\Entity\profile $profile) {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new profileType(), $profile);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($profile);
            $profile->getImage()->manualRemove($profile->getImage()->getAbsolutePath());
            $profile->getImage()->upload();
               
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', "Vos modifications ont été enregistrées.");
            return $this->redirect($this->generateUrl('ben_profile_edit', array('name' => $profile->getId())));
        }
        $this->get('session')->getFlashBag()->add('error', "Il y a des erreurs dans le formulaire soumis !");

        return $this->render('BenUserBundle:myProfile:edit.html.twig', array(
                    'entity' => $profile,
                    'form' => $form->createView(),
                ));
    }

    /**
     * export to xml
     * @Secure(roles="ROLE_MANAGER")
     */    
    public function toXmlAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $entities = $em->getRepository('BenUserBundle:user')->getUsers();
        // return $this->render('BenUserBundle:admin:list.xml.twig', array('entities' => $entities));

        $response = $this->render('BenUserBundle:admin:list.xml.twig',array(
                    'entities' => $entities,
                    ));
         $response->headers->set('Content-Type', 'text/xml');
         // $response->headers->set('Content-Disposition', 'attachment; filename="contacts.xml"');

        return $response;
    }

    /**
     * export to pdf
     * @Secure(roles="ROLE_USER")
     */
    public function toPdfAction($users)
    {
        if(!$users)
            return $this->redirect($this->generateUrl('ben_users'));
        $em = $this->getDoctrine()->getManager();

        if($users != 'all'){
            $users_id = explode(',', $users);
            $entities = $em->getRepository('BenUserBundle:user')->findUserById($users_id);
        }
        else $entities = $em->getRepository('BenUserBundle:user')->findAll();
        // return $this->render('BenUserBundle:admin:badge.html.twig', array('entities' => $entities));

        $now = new \DateTime;
        $now = $now->format('d-m-Y_H-i');
        $html = $this->renderView('BenUserBundle:admin:badge.html.twig', array(
            'entities' => $entities));

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
     * export to excel
     * @Secure(roles="ROLE_USER")
     */
    public function toExcelAction($status)
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('BenUserBundle:user')->findAll();
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("ben");
       $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue("A1", "id")
            ->setCellValue("B1", "nom d'utilisateur")
            ->setCellValue("C1", "nom")
            ->setCellValue("D1", "prenom")
            ->setCellValue("E1", "ci")
            ->setCellValue("F1", "email")
            ->setCellValue("G1", "Sexe")
            ->setCellValue("H1", "Date de naissance")
            ->setCellValue("I1", "Adresse")
            ->setCellValue("J1", "Ville")
            ->setCellValue("K1", "Code postal")
            ->setCellValue("L1", "Pays")
            ->setCellValue("M1", "Profession")
            ->setCellValue("N1", "A propos")
            ->setCellValue("O1", "Tél")
            ->setCellValue("P1", "Gsm")
            ->setCellValue("Q1", "Revenu des parents")
            ->setCellValue("R1", "Année d'obtention du bac")
            ->setCellValue("S1", "Note du Baccalauréat")
            ->setCellValue("T1", "Nombre des fréres/soeurs")
            ->setCellValue("U1", "Note de lgement")
            ->setCellValue("V1", "Etat")
            ->setCellValue("W1", "Comportement")
            ->setCellValue("X1", "Remarque")
            ->setCellValue("Y1", "Date 'inscription");
       $i=2;
       foreach ($entities as $entity) {
            $university = ($entity->getEtablissement()) ? $entity->getEtablissement()->getName() : '';
           $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue("A$i", $entity->getId())
                ->setCellValue("B$i", $entity->getNDossier())
                ->setCellValue("C$i", $entity->getFamilyName())
                ->setCellValue("D$i", $entity->getFirstName())
                ->setCellValue("E$i", $entity->getCin())
                ->setCellValue("F$i", $entity->getCne())
                ->setCellValue("G$i", $entity->getPassport())
                ->setCellValue("H$i", $entity->getCarteSejour())
                ->setCellValue("I$i", $entity->getBirdDay()->format('d/m/Y'))
                ->setCellValue("J$i", $entity->getGender())
                ->setCellValue("K$i", $entity->getAncientete())
                ->setCellValue("L$i", $entity->getContry())
                ->setCellValue("M$i", $entity->getCity())
                ->setCellValue("N$i", $university)
                ->setCellValue("O$i", $entity->getDiplome())
                ->setCellValue("P$i", $entity->getNiveauEtude())
                ->setCellValue("Q$i", $entity->getParentsRevenu())
                ->setCellValue("R$i", $entity->getObtentionBac())
                ->setCellValue("S$i", $entity->getExellence())
                ->setCellValue("T$i", $entity->getBroSisNumber())
                ->setCellValue("U$i", $entity->getNote())
                ->setCellValue("V$i", $entity->getStatus())
                ->setCellValue("W$i", $entity->getConditionSpecial())
                ->setCellValue("X$i", $entity->getRemarque())
                ->setCellValue("Y$i", $entity->getCreated()->format('d/m/Y'));
            $i++;
       }

       $phpExcelObject->getActiveSheet()->setTitle('Liste des adhérents');

       // Set active sheet index to the first sheet, so Excel opens this as the first sheet
       $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $now = new \DateTime;
        $now = $now->format('d-m-Y_H-i');
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', "attachment;filename=members-$now.xls");
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;        
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * generate etiquette pdf
     * @Secure(roles="ROLE_USER")
     */
    public function etiquetteAction($users)
    {
        if(!$users)
            return $this->redirect($this->generateUrl('ben_users'));
        $em = $this->getDoctrine()->getManager();

        if($users != 'all'){
            $users_id = explode(',', $users);
            $entities = $em->getRepository('BenUserBundle:user')->findUserById($users_id);
        }
        else $entities = $em->getRepository('BenUserBundle:user')->findAll();
        // return $this->render('BenUserBundle:admin:etiquette.html.twig', array('entities' => $entities));

        $now = new \DateTime;
        $now = $now->format('d-m-Y_H-i');
        $html = $this->renderView('BenUserBundle:admin:etiquette.html.twig', array(
            'entities' => $entities));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="etiquette'.$now.'.pdf"'
            )
        );
    }
}