<?php

namespace Ben\AssociationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ben\AssociationBundle\Entity\Hotels
 *
 * @ORM\Table(name="hotels")
 * @ORM\Entity
 */
class Hotels
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $category
     *
     * @ORM\Column(name="category", type="string", length=255)
     */
    private $category;

    /**
     * @var string $address
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var string $city
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var string $post_code
     *
     * @ORM\Column(name="post_code", type="string", length=255)
     */
    private $post_code;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
    * @ORM\OneToMany(targetEntity="Ben\AssociationBundle\Entity\Rooms", mappedBy="hotel", cascade={"remove", "persist"})
    */
    private $rooms;

    
    /************ constructeur ************/
    
    public function __construct()
    {
        $this->rooms = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return hotel
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
     * Set category
     *
     * @param string $category
     * @return hotel
     */
    public function setCategory($category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return hotel
     */
    public function setAddress($address)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Hotels
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

    /**
     * Set post_code
     *
     * @param string $postCode
     * @return Hotels
     */
    public function setPostCode($postCode)
    {
        $this->post_code = $postCode;
    
        return $this;
    }

    /**
     * Get post_code
     *
     * @return string 
     */
    public function getPostCode()
    {
        return $this->post_code;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return hotel
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
     * Add room
     *
     * @param \Ben\AssociationBundle\Entity\Rooms $room
     * @return category
     */
    public function addRoom(\Ben\AssociationBundle\Entity\Rooms $room)
    {
        $this->rooms[] = $room;
    
        return $this;
    }

    /**
     * Remove room
     *
     * @param \Ben\AssociationBundle\Entity\Rooms $room
     */
    public function removeRoom(\Ben\AssociationBundle\Entity\Rooms $room)
    {
        $this->rooms->removeElement($room);
    }

    /**
     * Get room
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    public function __toString()
    {
        return $this->name;
    }
}