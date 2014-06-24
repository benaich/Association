<?php

namespace Ben\UserBundle\Entity;

use FOS\UserBundle\Entity\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="mygroup")
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
     * @ORM\ManyToMany(targetEntity="Ben\UserBundle\Entity\User", mappedBy="groups")
     * @ORM\JoinTable(name="user_group")
     */
    protected $users;

    /**
     * @ORM\ManyToMany(targetEntity="Ben\AssociationBundle\Entity\event", mappedBy="groups")
     * @ORM\JoinTable(name="event_group")
     */
    protected $events;

    public function __construct() {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
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
}