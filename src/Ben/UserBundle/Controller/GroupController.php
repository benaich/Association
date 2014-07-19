<?php
namespace Ben\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;
use FOS\UserBundle\Controller\GroupController as BaseController;
use Ben\UserBundle\Entity\Group;
use Ben\UserBundle\Form\GroupType;

class GroupController extends BaseController
{
    /**
     * Show all groups
     * @Secure(roles="ROLE_MANAGER")
     */
    public function listAction()
    {
        $groups = $this->container->get('fos_user.group_manager')->findGroups();
        $group = new Group();
        $form = $this->container->get('form.factory')->create(new GroupType(), $group);

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:list.html.'.$this->getEngine(),
         array('groups' => $groups, 'group' => $group, 'form' => $form->createview()));
    }

    /**
     * Show one group
     * @Secure(roles="ROLE_MANAGER")
     */
    public function showAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:show.html.'.$this->getEngine(), array('group' => $group));
    }

    /**
     * Edit one group, show the edit form
     * @Secure(roles="ROLE_MANAGER")
     */
    public function editAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);

        $em = $this->container->get('doctrine')->getEntityManager();
        $form = $this->container->get('form.factory')->create(new GroupType(), $group);
        $request = $this->container->get('request');
        if($request->getMethod() === 'POST'){
            $form->bind($request);
            if ($form->isValid()) {
                $group->getImage()->manualRemove($group->getImage()->getAbsolutePath());
                $group->getImage()->upload();
                $em->persist($group);
                $em->flush();

                $this->setFlash('success', 'ben.flash.success.group.updated');
                // return new RedirectResponse($this->container->get('router')->generate('fos_user_group_list'));
            }
            else $this->setFlash('error', 'ben.flash.error.form');
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Group:edit.html.twig', array(
            'form'      => $form->createview(),
            'group'  => $group,
        ));
    }

    /**
     * Show the new form
     * @Secure(roles="ROLE_MANAGER")
     */
    public function newAction()
    {
        $em = $this->container->get('doctrine')->getEntityManager();
        $group = new Group();
        $form = $this->container->get('form.factory')->create(new GroupType(), $group);
        $form->bind($this->container->get('request'));
        if ($form->isValid()) {
            $group->setRoles(array());
            $group->getImage()->upload();
            $em->persist($group);
            $em->flush();

            $this->setFlash('success', 'ben.flash.success.group.created');
            return new RedirectResponse($this->container->get('router')->generate('fos_user_group_list'));
        }

        $this->setFlash('danger', 'ben.flash.error.form');
        return new RedirectResponse($this->container->get('router')->generate('fos_user_group_list'));

        // var_dump($this->getErrorMessages($form));die;
    }

    /**
     * Delete one group
     * @Secure(roles="ROLE_MANAGER")
     */
    public function deleteAction($groupname)
    {
        $group = $this->findGroupBy('name', $groupname);
        $this->container->get('fos_user.group_manager')->deleteGroup($group);
        $this->setFlash('success', 'ben.flash.success.group.deleted');

        return new RedirectResponse($this->container->get('router')->generate('fos_user_group_list'));
    }

    /**
     * Find a group by a specific property
     *
     * @param string $key   property name
     * @param mixed  $value property value
     *
     * @throws NotFoundException                    if user does not exist
     * @return \FOS\UserBundle\Model\GroupInterface
     */
    protected function findGroupBy($key, $value)
    {
        if (!empty($value)) {
            $group = $this->container->get('fos_user.group_manager')->{'findGroupBy'.ucfirst($key)}($value);
        }

        if (empty($group)) {
            throw new NotFoundHttpException(sprintf('The group with "%s" does not exist for value "%s"', $key, $value));
        }

        return $group;
    }

    protected function getEngine()
    {
        return $this->container->getParameter('fos_user.template.engine');
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value)
    {
        $this->container->get('session')->getFlashBag()->set($action, $value);
    }

    private function getErrorMessages(\Symfony\Component\Form\Form $form) {      
        $errors = array();

        if ($form->hasChildren()) {
            foreach ($form->getChildren() as $child) {
                if (!$child->isValid()) {
                    $errors[$child->getName()] = $this->getErrorMessages($child);
                }
            }
        } else {
            foreach ($form->getErrors() as $key => $error) {
                $errors[] = $error->getMessage();
            }   
        }

        return $errors;
    }
}
