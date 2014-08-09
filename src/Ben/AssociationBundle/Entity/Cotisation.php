<?php

namespace Ben\AssociationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cotisation
 *
 * @ORM\Table(name="cotisation")
 * @ORM\Entity(repositoryClass="Ben\AssociationBundle\Entity\CotisationRepository")
 */
class Cotisation
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
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_from", type="date")
     */
    private $date_from;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_to", type="date")
     */
    private $date_to;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="date")
     */
    private $created;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
    * @ORM\ManyToOne(targetEntity="Ben\UserBundle\Entity\User",inversedBy="cotisations")
    * @ORM\JoinColumn(name="user_id",referencedColumnName="id", nullable=false)
    */
    private $user;

    /************ constructeur ************/

    public function __construct() {
        $this->date_from = new \DateTime;
        $this->date_to = new \DateTime;
        $this->created = new \DateTime;
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
     * Set price
     *
     * @param float $price
     * @return Cotisation
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Cotisation
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set date_from
     *
     * @param \DateTime $dateFrom
     * @return Cotisation
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
     * @return Cotisation
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
     * Set created
     *
     * @param \DateTime $created
     * @return Cotisation
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Cotisation
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set user
     *
     * @param \Ben\UserBundle\Entity\User $user
     * @return Cotisation
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

    public function get($value)
    {
        $value = 'get'.ucfirst($value);
        if($value == 'getUser') 
            $value = $this->user->getProfile()->getFullName();
        else $value = $this->$value();

        return ($value instanceof \DateTime) ? $value->format('Y-m-d') : $value;
    }

    public function toArray()
    {
        return array(
            'id'=>$this->id,
            'description'=>$this->description,
            'type'=>$this->type,
            'start' => $this->date_from->format('Y-m-d H:i:s'), 
            'end' => $this->date_to->format('Y-m-d H:i:s')
            );
    }
}