<?php

namespace Ben\AssociationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ben\AssociationBundle\Entity\Avancement
 *
 * @ORM\Table(name="avancement")
 * @ORM\Entity(repositoryClass="Ben\AssociationBundle\Entity\AvancementRepository")
 */
class Avancement
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
     * @var \DateTime $date_from
     *
     * @ORM\Column(name="date_from", type="date")
     */
    private $date_from;

    /**
     * @var \DateTime $date_to
     *
     * @ORM\Column(name="date_to", type="date")
     */
    private $date_to;

    /**
     * @var string $city
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
    * @ORM\ManyToOne(targetEntity="Ben\UserBundle\Entity\User", inversedBy="avancements")
    * @ORM\JoinColumn(name="user_id",referencedColumnName="id", nullable=false)
    */
    protected $user;

    /**
    * @ORM\ManyToOne(targetEntity="Ben\AssociationBundle\Entity\Status", inversedBy="avancements")
    * @ORM\JoinColumn(name="status_id",referencedColumnName="id", nullable=false)
    */
    protected $status;
    
    /************ constructeur ************/
    
    public function __construct()
    {
        $this->date_from = new \DateTime;
        $this->date_to = new \DateTime;
        $this->status = true;
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
     * Set date_from
     *
     * @param \DateTime $dateFrom
     * @return Avancement
     */
    public function setDateFrom($dateFrom)
    {
        $this->date_from = $dateFrom;
    
        return $this;
    }

    /**
     * Get date_from
     *
     * @return \DateTime 
     */
    public function getDateFrom()
    {
        return $this->date_from;
    }

    /**
     * Set date_to
     *
     * @param \DateTime $dateTo
     * @return Avancement
     */
    public function setDateTo($dateTo)
    {
        $this->date_to = $dateTo;
    
        return $this;
    }

    /**
     * Get date_to
     *
     * @return \DateTime 
     */
    public function getDateTo()
    {
        return $this->date_to;
    }

    /**
     * Set user
     *
     * @param \Ben\UserBundle\Entity\User $user
     * @return Avancement
     */
    public function setUser(\Ben\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Ben\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set status
     *
     * @param \Ben\AssociationBundle\Entity\Status $status
     * @return Avancement
     */
    public function setStatus(\Ben\AssociationBundle\Entity\Status $status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return \Ben\AssociationBundle\Entity\Status 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Avancement
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }
}