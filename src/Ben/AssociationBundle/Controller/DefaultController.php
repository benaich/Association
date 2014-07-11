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
        $counter['user'] = $em->getRepository('BenUserBundle:User')->counter()[1];
        $counter['group'] = count($this->container->get('fos_user.group_manager')->findGroups());
        $counter['event'] = $em->getRepository('BenAssociationBundle:event')->counter()[1];
        $counter['reservation'] = $em->getRepository('BenAssociationBundle:Reservation')->counter()[1];
        $counter['rooms'] = $em->getRepository('BenAssociationBundle:Rooms')->counter()[1];
        $counter['hotels'] = 2;
        return $this->render('BenAssociationBundle:Default:index.html.twig', array(
                'counter' => $counter));
    }
}
