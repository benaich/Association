<?php

namespace Ben\AssociationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

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
        // $counter['reservation'] = $em->getRepository('BenAssociationBundle:Reservation')->counter();
        // $counter['rooms'] = $em->getRepository('BenAssociationBundle:Rooms')->counter();
        // $counter['hotels'] = 2;
        return $this->render('BenAssociationBundle:Default:index.html.twig', array(
                'counter' => $counter));
    }
}
