<?php

namespace Ben\AssociationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Ben\UserBundle\Entity\User;
use Ben\UserBundle\Entity\Group;

class DefaultController extends Controller
{
    /**
     * @Secure(roles="ROLE_MANAGER")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $counter['user'] = $em->getRepository('BenUserBundle:User')->counter();
        $counter['group'] = count($this->container->get('fos_user.group_manager')->findGroups());
        $counter['event'] = $em->getRepository('BenAssociationBundle:event')->counter();
        $counter['cotisation'] = $em->getRepository('BenAssociationBundle:cotisation')->counter();
        $counter['status'] = $em->getRepository('BenAssociationBundle:status')->counter();

        $stats['status'] = $em->getRepository('BenUserBundle:User')->statsByStatus();
        $stats['city'] = $em->getRepository('BenUserBundle:User')->statsByCity();
        $total = 0;
        foreach ($stats['city'] as $obj) {
            $total += $obj['data'];
        }

        $stats['city'] = array_map(function($obj) use ($total){
            $obj['percentage'] = $obj['data'] * 100 / $total;
            return $obj;
        }, $stats['city']);
        
        $groups = $em->getRepository('BenUserBundle:Group')->findByType(Group::$COMMISSION);
        return $this->render('BenAssociationBundle:Default:index.html.twig', array(
                'groups' => $groups,
                'stats' => $stats,
                'counter' => $counter));
    }


    /**
     * @Secure(roles="ROLE_MANAGER")
     */
    public function inmportFromCsvAction(Request $request)
    {
        $form = $this->createFormBuilder()
        ->add('submitFile', 'file')
        ->getForm();

        if ($request->getMethod('post') == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                 $file = $form->get('submitFile');
                 $data = $file->getData()->getPathname();
                 try {
                    if (($handle = fopen($file->getData()->getPathname(), "r")) !== FALSE) {
                        $keys = fgetcsv($handle);
                        $counter = 0;
                        while(($row = fgetcsv($handle)) !== FALSE) {
                            $data = array_combine($keys, $row);
                            $entity = new User();
                            $entity->setData($data);
                            $em = $this->get('fos_user.user_manager');
                            $em->updateUser($entity, false);
                            $counter++;
                        }
                        
                        try {
                            $this->getDoctrine()->getManager()->flush();
                            $this->get('session')->getFlashBag()->add('success', "$counter adherant ont été ajouté à la base de données");
                        } catch (\Doctrine\DBAL\DBALException $e) {
                            $this->get('session')->getFlashBag()->add('error', "duplication des informations");
                        }
                    }
                 } catch (\Exception $e) {
                     $this->get('session')->getFlashBag()->add('error', "ben.flash.error.general");
                 }
                }else $this->get('session')->getFlashBag()->add('error', "ben.flash.error.form");

         }

        return $this->render('BenAssociationBundle:Default:import.html.twig',
            array('form' => $form->createView(),)
        );
    }
}
