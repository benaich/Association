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
     * Finds and displays a config entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:config')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find config entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BenAssociationBundle:config:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new config entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function newAction()
    {
        $entity = new config();
        $form   = $this->createForm(new configType(), $entity);

        return $this->render('BenAssociationBundle:config:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new config entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new config();
        $form = $this->createForm(new configType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('config_show', array('id' => $entity->getId())));
        }

        return $this->render('BenAssociationBundle:config:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing config entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('BenAssociationBundle:config')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find config entity.');
        }

        $editForm = $this->createForm(new configType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('BenAssociationBundle:config:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing config entity.
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

        foreach ($config as $key => $value) {
            $em->getRepository('BenAssociationBundle:config')->updateBy($key, $value);
        }
        return $this->redirect($this->generateUrl('config'));
    }

    /**
     * Deletes a config entity.
     * @Secure(roles="ROLE_MANAGER")
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('BenAssociationBundle:config')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find config entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('config'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
