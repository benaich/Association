<?php

namespace Ben\AssociationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Status
 *
 * @ORM\Table(name="status")
 * @ORM\Entity(repositoryClass="Ben\AssociationBundle\Entity\StatusRepository")
 */
class Status
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
    * @ORM\OneToMany(targetEntity="Ben\AssociationBundle\Entity\Avancement", mappedBy="status", cascade={"remove", "persist"})
    */
    private $avancements;
    
    /************ constructeur ************/
    
    public function __construct()
    {
        $this->avancements = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /************ getters & setters  ************/

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
     * Set name
     *
     * @param string $name
     * @return Status
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Add avancements
     *
     * @param \Ben\AssociationBundle\Entity\Avancement $avancements
     * @return Status
     */
    public function addAvancement(\Ben\AssociationBundle\Entity\Avancement $avancements)
    {
        $this->avancements[] = $avancements;
    
        return $this;
    }

    /**
     * Remove avancements
     *
     * @param \Ben\AssociationBundle\Entity\Avancement $avancements
     */
    public function removeAvancement(\Ben\AssociationBundle\Entity\Avancement $avancements)
    {
        $this->avancements->removeElement($avancements);
    }

    /**
     * Get avancements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAvancements()
    {
        return $this->avancements;
    }
}