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
        $counter['cotisation'] = $em->getRepository('BenAssociationBundle:Cotisation')->counter();
        $counter['status'] = $em->getRepository('BenAssociationBundle:Status')->counter();

        $groups = $em->getRepository('BenUserBundle:Group')->findByType(Group::$COMMISSION);
        return $this->render('BenAssociationBundle:Default:index.html.twig', array(
                'groups' => $groups,
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

    /**
     * @Secure(roles="ROLE_MANAGER")
     */
    public function statsAction()
    {
        $statsHandler = $this->get('ben.stats_handler');

        $stats['city'] = $statsHandler->setDataColumn('city')->processData();
        $stats['gender'] = $statsHandler->setDataColumn('gender')->processData();
        $stats['status'] = $statsHandler->setDataColumn('status')->processData();
        $stats['created'] = $statsHandler->setDataColumn('created')->processData();
        $stats['created'] = array_map(function($item){
                return array((new \DateTime($item['x']))->getTimestamp()*1000, 0+$item['y']);
            }, $stats['created']);
        
        $stats['revenu'] = $statsHandler->setDataColumn('revenu')->processData();
        $stats['revenu'] = array_map(function($item){
                return array((new \DateTime($item['x']))->getTimestamp()*1000, 0+$item['y']);
            }, $stats['revenu']);

        $cot = $statsHandler->setDataColumn('cotisation')->processData();
        $stats['cotisation'] = array(
                array('label' => 'A jour', 'data' => $cot['yes'], 'color' => '#93b40f'),
                array('label' => 'En retard', 'data' => $cot['no'], 'color' => '#e1ab0b'),
                array('label' => 'N\'a jamais cotisé', 'data' => $cot['never'], 'color' => '#b94a48')
                );
        // var_dump($stats);die;


        $em = $this->getDoctrine()->getManager();
        $status = $em->getRepository('BenAssociationBundle:Status')->findAll();
        $groups = $em->getRepository('BenUserBundle:Group')->findAll();
        return $this->render('BenAssociationBundle:Default:stats.html.twig', array(
            'status' => $status,
            'groups' => $groups,
            'stats' => $stats));
    }

    /**
     * @Secure(roles="ROLE_MANAGER")
     */
    public function jsonStatsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $searchParam = $request->get('searchParam');
        $all = $em->getRepository('BenUserBundle:User')->counter();
        $result = $em->getRepository('BenUserBundle:User')->counter(0, $searchParam);
        $response = new Response(json_encode(array(
                array('label' => $searchParam['label'], 'data' => $result, 'color' => '#058dc7'),
                array('label' => 'Autres', 'data' => ($all-$result), 'color' => '#E9E9E9')
                )));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}
