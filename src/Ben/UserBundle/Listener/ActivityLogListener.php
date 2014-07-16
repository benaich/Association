<?php
namespace Ben\UserBundle\Listener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpKernel\HttpKernel;
use Ben\UserBundle\Entity\User;
use Ben\UserBundle\Entity\ActivityLog;
 
class ActivityLogListener
{
    protected $_container;
    protected $_em;
 
    public function __construct(ContainerInterface $container)
    {
        $this->_container = $container;
    }

    /**
     * @param string $repo
     * @return Object
     */
    protected function _em($repo = '')
    {
        $em = $this->_em ? : $this->_em = $this->_container->get('doctrine.orm.entity_manager');
        if (!empty($repo))
            $em = $em->getRepository($repo);
 
        return $em;
    }
 
    
    /**
     * @return integer
     */
    protected function _getUserId()
    {
        if ($this->_container->get('security.context')->getToken())
        {
            $user = $this->_container->get('security.context')->getToken()->getUser();
 
            if (method_exists($user, 'getId'))
            {
                return $user->getId();
            }
            elseif ($user != 'anon.')
            {
                return 0;
            }
        }
    }

    /**
    * Entity classes used by the Activity Log
    *
    * @return array
    */
    private $_validClasses = array(
        'Ben\UserBundle\Entity\User',
        'Acme\UserBundle\Entity\User'
    );
 
    /**
     * Listener attached to newly created records
     * @return bool
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        if (!is_null( $this->_getUserId() ))
            $this->_checkLog($args);
 
        return true;
    }


    /**
     * Listener attached to updated records
     *
     * @return bool
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        if (!is_null( $this->_getUserId() ))
            $this->_checkLog($args);
 
        return true;
    }



     /**
     * Listener attached to removed records
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     * @return bool
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        if (!is_null( $this->_getUserId() ))
            $this->_container->get('session')->set('entity_remove', $args->getEntity());
 
        return true;
    }
 
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $this->_container->get('session')->get('entity_remove');
        if (!empty($entity))
        {
            $this->_checkLog($entity, array('delete' => true, 'entity' => true));
 
            $this->_container->get('session')->remove('entity_remove');
        }
    }
 
    /**
     * @param $args
     * @param array $options
     * @return bool
     */
    public function _checkLog($args, $options = array())
    {
        $entity_class = empty($options['entity']) ? $args->getEntity() : $args;
 
        if (get_class($entity_class) == 'Ben\UserBundle\Entity\ActivityLog')
            return false;
 
        $class = get_class($entity_class);
 
        if (in_array( $class, $this->_validClasses ))
        { 
            $log = $this->_em('BenUserBundle:ActivityLog')->findOneBy(array(
                'classname' => $class,
                'entity_id' => $entity_class->getId()
            ));
 
            if (empty($log))
            {
                if (empty($options['delete']))
                    $this->_addLog($entity_class, $class);
            }
            else
            {
                if (empty($options['delete']))
                {
                    $this->_updateLog($log);
                }
                else
                {
                    $this->_removeLog($log);
                }
            }
        }
    }
 
    /**
     * @param $entity_class
     * @param $class
     */
    public function _addLog($entity_class, $class)
    {
        $entity = new ActivityLog;
 
        $entity->setClassName( $class );
        $entity->setEntityId($entity_class->getId());
        $entity->setUser( $this->_getUserId() );
 
        $this->_em()->persist($entity);
        $this->_em()->flush();
    }
 
    /**
     * @param $entity
     */
    public function _updateLog($entity)
    {
        $entity->setUser( $this->_getUserId() );
        $entity->setDate( new \DateTime() );
 
        $this->_em()->persist($entity);
        $this->_em()->flush();
    }
 
    /**
     * @param $entity
     * @return bool
     */
    public function _removeLog($entity)
    {
        $this->_em()->remove($entity);
        $this->_em()->flush();
 
        return false;
    }
}