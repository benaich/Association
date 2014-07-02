<?php

namespace Ben\AssociationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ben\AssociationBundle\Entity\Rooms
 *
 * @ORM\Table(name="rooms")
 * @ORM\Entity(repositoryClass="Ben\AssociationBundle\Entity\RoomsRepository")
 */
class Rooms
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
     * @var integer $number
     *
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var integer $floor
     *
     * @ORM\Column(name="floor", type="integer")
     */
    private $floor;

    /**
     * @var integer $max
     *
     * @ORM\Column(name="max", type="integer")
     */
    private $max;

    /**
     * @var integer $free
     *
     * @ORM\Column(name="free", type="integer")
     */
    private $free;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;
    
    /**
    * @ORM\ManyToOne(targetEntity="Ben\AssociationBundle\Entity\Hotels",inversedBy="rooms")
    * @ORM\JoinColumn(name="hotel_id",referencedColumnName="id", nullable=false)
    * @Assert\Valid()
    */
    private $hotel;
    
    /**
    * @ORM\OneToMany(targetEntity="Ben\AssociationBundle\Entity\Reservation",mappedBy="room", cascade={"remove", "persist"})
    */
    private $reservations;
    
    /************ constructeur ************/
    
    public function __construct()
    {
        $this->reservations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set number
     *
     * @param integer $number
     * @return room
     */
    public function setNumber($number)
    {
        $this->number = $number;
    
        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set max
     *
     * @param integer $max
     * @return room
     */
    public function setMax($max)
    {
        $this->max = $max;
    
        return $this;
    }

    /**
     * Get max
     *
     * @return integer 
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return room
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
     * Set hotel
     *
     * @param \Ben\AssociationBundle\Entity\Hotels $hotel
     * @return posts
     */
    public function setHotel(\Ben\AssociationBundle\Entity\Hotels $hotel)
    {
        $this->hotel = $hotel;
    
        return $this;
    }

    /**
     * Get hotel
     *
     * @return \Ben\AssociationBundle\Entity\Hotels 
     */
    public function getHotel()
    {
        return $this->hotel;
    }

    /**
     * Set floor
     *
     * @param integer $floor
     * @return Rooms
     */
    public function setFloor($floor)
    {
        $this->floor = $floor;
    
        return $this;
    }

    /**
     * Get floor
     *
     * @return integer 
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * + free
     *
     * @param integer $free
     * @return Rooms
     */
    public function plusFree()
    {
        $this->free++;
    
        return $this;
    }

    /**
     * - free
     *
     * @param integer $free
     * @return Rooms
     */
    public function minusFree()
    {
        $this->free--;
    
        return $this;
    }

    /**
     * is free
     *
     * @return integer 
     */
    public function isFree()
    {
        return ($this->free > 0);
    }

    /**
     * Set free
     *
     * @param integer $free
     * @return Rooms
     */
    public function setFree($free)
    {
        $this->free = $free;
    
        return $this;
    }

    /**
     * Get free
     *
     * @return integer 
     */
    public function getFree()
    {
        return $this->free;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return ($this->free > 0) ? 'ouverte' : 'réservé';
    }

    /**
     * Add reservation
     *
     * @param Ben\AssociationBundle\Entity\Reservation $reservation
     * @return Rooms
     */
    public function addReservation(\Ben\AssociationBundle\Entity\Reservation $reservation)
    {
        $this->reservations[] = $reservation;
        $reservation->setRoom($this);
    
        return $this;
    }

    /**
     * Remove reservations
     *
     * @param Ben\AssociationBundle\Entity\Reservation $reservations
     */
    public function removeReservation(\Ben\AssociationBundle\Entity\Reservation $reservation)
    {
        $this->reservations->removeElement($reservation);
    }

    /**
     * Get reservations
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    public function __toString()
    {
        return ''.$this->number;
    }

    /* to array */
    public function toArray()
    {
        return array(
           'id' => $this->getId(),
           'number' => $this->getNumber(),
           'floor' => $this->getFloor(),
           'max' => $this->getMax(),
           'free' => $this->getfree(),
           'type' => $this->getType(),
           );
    }
}