<?php

namespace Ben\AssociationBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Ben\AssociationBundle\Entity\config;
use Ben\AssociationBundle\Form\configType;

/**
 * config controller.
 *
 */
class configController extends Controller
{
    /**
     * Lists all config entities.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('BenAssociationBundle:config')->findAll();
        $config=[];

        foreach ($entities as $entity) {
           $config[$entity->getTheKey()] = $entity->getTheValue();
        }
        $img = new \Ben\AssociationBundle\Entity\image();
        $imgform   = $this->createForm(new \Ben\AssociationBundle\Form\imageType(), $img);

        return $this->render('BenAssociationBundle:config:index.html.twig', array(
            'config' => $config,
            'imgform' => $imgform->createView()
        ));
    }

    /**
     * update all config entities.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $config = $request->get('config');

        /* handle img */
        $img = new \Ben\AssociationBundle\Entity\image();
        $imgform   = $this->createForm(new \Ben\AssociationBundle\Form\imageType(), $img);
        $imgform->bind($request);
        if($img->upload())
            $config['org_logo'] = $img->getWebPath();

        $config['org_signup'] = isset($config['org_signup']);
        $config['print_permission'] = isset($config['print_permission']);
        $config['allowaccess'] = isset($config['allowaccess']);
        foreach ($config as $key => $value) {
            $em->getRepository('BenAssociationBundle:config')->updateBy($key, $value);
        }

        $this->get('session')->getFlashBag()->add('success', "ben.flash.success.updated");
        return $this->redirect($this->generateUrl('config'));
    }


    /**
     * update all config entities.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function updateLangAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $request->get('_locale');
        $this->get('session')->set('_locale', $locale);

        $em->getRepository('BenAssociationBundle:config')->updateBy('org_lang', $locale);

        $this->get('session')->getFlashBag()->add('success', "ben.flash.success.updated");
        return $this->redirect($this->generateUrl('fos_user_profile_edit'));
    }
    /**
     * update all fields entities.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function updateFiledsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if($request->getMethod() === 'POST'){         
            $config = $request->get('config');
            foreach ($config as $cfg) {
                $em->getRepository('BenAssociationBundle:Fields')->updateFields($cfg);
            }
        }

        return $this->render('BenAssociationBundle:config:fields.html.twig');
    }
}
