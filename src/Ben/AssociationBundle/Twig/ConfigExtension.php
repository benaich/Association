<?php

namespace Ben\AssociationBundle\Twig;
use Doctrine\ORM\EntityManager;

class ConfigExtension extends \Twig_Extension {

    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getGlobals()
    {
        $config = $this->em->getRepository('BenAssociationBundle:config')->findAll();
        $fields = $this->em->getRepository('BenAssociationBundle:Fields')->findAll();
        $userTable = array_filter($fields, function($obj){
            return ($obj->getTableName() === 'adherant');
        });
        $cotisationTable = array_filter($fields, function($obj){
            return ($obj->getTableName() === 'cotisation');
        });
        $result['userTable']= $userTable;
        $result['cotisationTable']= $cotisationTable;
        foreach ($config as $cf) {
            $result[$cf->getTheKey()] = $cf->getTheValue();
        }
      return array(
        'app_config'=> $result,
      );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName() {
        return 'config_extension';
    }

}