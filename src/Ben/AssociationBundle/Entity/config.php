<?php

namespace Ben\AssociationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ben\AssociationBundle\Entity\config
 *
 * @ORM\Table(name="config")
 * @ORM\Entity(repositoryClass="Ben\AssociationBundle\Entity\configRepository")
 */
class config
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $the_key
     *
     * @ORM\Column(name="the_key", type="string", length=255)
     */
    private $the_key;

    /**
     * @var string $the_value
     *
     * @ORM\Column(name="the_value", type="text")
     */
    private $the_value;

    public function __construct($the_key='', $the_value='') {
        $this->the_key = $the_key;
        $this->the_value = $the_value;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the_key
     *
     * @param string $theKey
     * @return config
     */
    public function setTheKey($theKey)
    {
        $this->the_key = $theKey;
    
        return $this;
    }

    /**
     * Get the_key
     *
     * @return string 
     */
    public function getTheKey()
    {
        return $this->the_key;
    }

    /**
     * Set the_value
     *
     * @param string $theValue
     * @return config
     */
    public function setTheValue($theValue)
    {
        $this->the_value = $theValue;
    
        return $this;
    }

    /**
     * Get the_value
     *
     * @return string 
     */
    public function getTheValue()
    {
        return $this->the_value;
    }
}
