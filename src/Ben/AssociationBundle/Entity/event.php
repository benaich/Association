<?php

namespace Ben\AssociationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ben\AssociationBundle\Entity\event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="Ben\AssociationBundle\Entity\eventRepository")
 */
class event
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
     * @var \DateTime $date_from
     *
     * @ORM\Column(name="date_from", type="datetime")
     */
    private $date_from;

    /**
     * @var \DateTime $date_to
     *
     * @ORM\Column(name="date_to", type="datetime")
     */
    private $date_to;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity="Ben\UserBundle\Entity\Group", inversedBy="events")
     * @ORM\JoinTable(name="event_group")
     */
    protected $groups;

    /************ Le constructeur ************/

    public function __construct() {
        $this->date_from = new \DateTime;
        $this->date_to = new \DateTime;
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /************ Les setters et getters ************/

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
     * @return event
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
     * Set date_from
     *
     * @param \DateTime $dateFrom
     * @return event
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
     * @return event
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
     * Set description
     *
     * @param string $description
     * @return event
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
     * Set type
     *
     * @param string $type
     * @return event
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
     * Add group
     *
     * @param Ben\UserBundle\Entity\Group $group
     * @return groups
     */
    public function addGroup(\Ben\UserBundle\Entity\Group $group)
    {
        $this->groups[] = $group;
    
        return $this;
    }

    /**
     * Remove groups
     *
     * @param Ben\UserBundle\Entity\Group $groups
     */
    public function removeGroup(\Ben\UserBundle\Entity\Group $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Get groups
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getGroupList()
    {
        $groups = [];
        foreach ($this->groups as $group) {
            $groups[] = $group->getName();
        }
        return implode(', ', $groups);
    }
    public function toArray()
    {
        return array(
            'id'=>$this->id,
            'title'=>$this->name,
            'description'=>$this->description,
            'type'=>$this->type,
            'groups'=>$this->getGroupList(),
            'start' => $this->date_from->format('Y-m-d H:i:s'), 
            'end' => $this->date_to->format('Y-m-d H:i:s'), 
            'url' => '#'
            );
    }
}