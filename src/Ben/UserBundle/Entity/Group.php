<?php

namespace Ben\UserBundle\Entity;

use FOS\UserBundle\Entity\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="mygroup")
 * @UniqueEntity("name")
 */
class Group extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Ben\UserBundle\Entity\User", mappedBy="groups", cascade={"persist"})
     * @ORM\JoinTable(name="user_group")
     */
    protected $users;

    /**
     * @ORM\ManyToMany(targetEntity="Ben\AssociationBundle\Entity\event", mappedBy="groups", cascade={"persist"})
     * @ORM\JoinTable(name="event_group")
     */
    protected $events;
    
    /**
    * @ORM\OneToOne(targetEntity="Ben\AssociationBundle\Entity\image", cascade={"remove", "persist"})
    * @Assert\Valid()
    */
    private $image;

    public function __construct() {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
        $this->image = new \Ben\AssociationBundle\Entity\image();
        $this->image->setPath('unknown.png');
    }

     public function __toString()
     {
     	return $this->name;
     }



    /**
     * Add user
     *
     * @param Ben\UserBundle\Entity\User $user
     * @return Rooms
     */
    public function addUser(\Ben\UserBundle\Entity\User $user)
    {
        $this->users[] = $user;
    
        return $this;
    }

    /**
     * Remove users
     *
     * @param Ben\UserBundle\Entity\User $users
     */
    public function removeUser(\Ben\UserBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add event
     *
     * @param Ben\AssociationBundle\Entity\event $event
     * @return Rooms
     */
    public function addEvent(\Ben\AssociationBundle\Entity\event $event)
    {
        $this->events[] = $event;
    
        return $this;
    }

    /**
     * Remove events
     *
     * @param Ben\AssociationBundle\Entity\event $events
     */
    public function removeEvent(\Ben\AssociationBundle\Entity\event $event)
    {
        $this->events->removeElement($event);
    }

    /**
     * Get events
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Set image
     *
     * @param \Ben\AssociationBundle\Entity\image $image
     * @return Group
     */
    public function setImage(\Ben\AssociationBundle\Entity\image $image = null)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return \Ben\AssociationBundle\Entity\image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get avatar
     *
     * @return string 
     */
    public function getImg() {
        return $this->getImage()->getwebpath();
    }

    /**
     * Get array
     *
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName()
            );
    }
}