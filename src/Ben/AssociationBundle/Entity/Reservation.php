<?php

namespace Ben\AssociationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ben\AssociationBundle\Entity\Reservation
 *
 * @ORM\Table(name="reservation")
 * @ORM\Entity(repositoryClass="Ben\AssociationBundle\Entity\ReservationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Reservation
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
     * @var string $status
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;
    
    /**
    * @ORM\ManyToOne(targetEntity="Ben\UserBundle\Entity\User",inversedBy="reservations")
    * @ORM\JoinColumn(name="user_id",referencedColumnName="id", nullable=false)
    */
    private $user;
    
    /**
    * @ORM\ManyToOne(targetEntity="Ben\AssociationBundle\Entity\Rooms", inversedBy="reservations")
    * @ORM\JoinColumn(name="room_id",referencedColumnName="id", nullable=false)
    */
    private $room;

    private $oldroom;

    /************ constructeur ************/

    public function __construct() {
        $this->date_from = new \DateTime;
        $this->date_to = new \DateTime;
        $this->status = 'valide';
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
     * @return Reservation
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
     * @return Reservation
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
     * Set status
     *
     * @param string $status
     * @return room
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set user
     *
     * @param Ben\UserBundle\Entity\User $user
     * @return Reservation
     */
    public function setUser(\Ben\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return Ben\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set room
     *
     * @param Ben\AssociationBundle\Entity\Rooms $room
     * @return Reservation
     */
    public function setRoom(\Ben\AssociationBundle\Entity\Rooms $room)
    {
        $this->room = $room;
    
        return $this;
    }

    /**
     * Get room
     *
     * @return Ben\AssociationBundle\Entity\Rooms 
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Set oldroom
     *
     * @param int $oldroom
     * @return Reservation
     */
    public function setOldroom($oldroom)
    {
        $this->oldroom = $oldroom;
    
        return $this;
    }

    /**
     * Get oldroom
     *
     * @return int 
     */
    public function getOldroom()
    {
        return $this->oldroom;
    }

    /**
     * @ORM\PreRemove()
     */
    public function freeRoom()
    {
        $this->room->plusFree();
    }
}