<?php

namespace Ben\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="mygroup")
 * @ORM\Entity(repositoryClass="Ben\UserBundle\Entity\GroupRepository")
 * @UniqueEntity("name")
 */
class Group
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;

    public static $SIMPLEGROUP  = 'groupe';
    public static $COMMISSION  = 'commission';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="kind", type="string", length=20)
     */
    private $type;

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
    
    /************ constructeur ************/

    public function __construct() {
        $this->type = Group::$SIMPLEGROUP;
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
        $this->image = new \Ben\AssociationBundle\Entity\image();
        $this->image->setPath('unknown.png');
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

    public function __toString()
    {
    	return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return group
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

    /**
     * Set type
     *
     * @param string $type
     * @return group
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