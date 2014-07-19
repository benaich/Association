<?php

namespace Ben\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use FOS\MessageBundle\Model\ParticipantInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="UserRepository"))
 * @ORM\Table(name="user")
 * @UniqueEntity("username")
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser implements ParticipantInterface {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime $created
     *
     * @ORM\Column(name="created", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $created;

    /**
     * @var \DateTime $lastActivity
     *
     * @ORM\Column(name="lastActivity", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $lastActivity;

    /**
     * @ORM\OneToOne(targetEntity="Ben\UserBundle\Entity\profile",cascade={"remove", "persist"})
     * @Assert\Valid()
     */
    protected $profile;

    /**
     * @ORM\ManyToMany(targetEntity="Ben\UserBundle\Entity\Group", inversedBy="users")
     * @ORM\JoinTable(name="user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
    * @ORM\OneToMany(targetEntity="Ben\AssociationBundle\Entity\Reservation", mappedBy="user", cascade={"remove", "persist"})
    */
    protected $reservations;

    /**
    * @ORM\OneToMany(targetEntity="Ben\AssociationBundle\Entity\Cotisation", mappedBy="user", cascade={"remove", "persist"})
    */
    protected $cotisations;
    
    /**
    * @ORM\OneToMany(targetEntity="Ben\AssociationBundle\Entity\Avancement", mappedBy="user", cascade={"remove", "persist"})
    */
    private $avancements;

    public function __construct() {
        parent::__construct();
        $this->created = new \DateTime;
        $this->lastActivity = new \DateTime;
        $this->profile = new \Ben\UserBundle\Entity\profile();
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reservations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * Set lastActivity
     *
     * @param \DateTime $lastActivity
     * @return User
     */
    public function setLastActivity($lastActivity) {
        $this->lastActivity = $lastActivity;

        return $this;
    }

    /**
     * Get lastActivity
     *
     * @return \DateTime 
     */
    public function getLastActivity() {
        return $this->lastActivity;
    }

    /**
     * Set lastActivity
     *
     * @param \DateTime $lastActivity
     * @return User
     */
    public function isActiveNow() {
        $this->lastActivity = new \DateTime();

        return $this;
    }

    /**
     * Set profile
     *
     * @param Ben\UserBundle\Entity\profile $profile
     * @return profile
     */
    public function setProfile(\Ben\UserBundle\Entity\profile $profile) {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return Ben\UserBundle\Entity\profile
     */
    public function getProfile() {
        return $this->profile;
    }

    /**
     * Get avatar
     *
     * @return string 
     */
    public function getAvatar() {
        return $this->getProfile()->getImage()->getwebpath();
    }

    /**
     * Add reservation
     *
     * @param Ben\AssociationBundle\Entity\Reservation $reservation
     * @return reservations
     */
    public function addReservation(\Ben\AssociationBundle\Entity\Reservation $reservation)
    {
        $this->reservations[] = $reservation;
        $reservation->setUser($this);
    
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

    /**
     * already has a reservation
     */
    public function hasReservation()
    {
        if(!count($this->reservations)) return false;
        return true ;
        // return ($this->reservations->lastEelement()->status === 'valide') ;
    }


    /**
     * Get reservations
     *
     * @return Ben\AssociationBundle\Entity\Reservation 
     */
    public function getReservation()
    {
        return $this->reservations->last();
    }
    
    /**
     * Get the most significant role
     *
     * @return string 
     */
    public function getRole()
    {
        $roles = ['ROLE_ADMIN', 'ROLE_MANAGER', 'ROLE_USER'];
        if(in_array('ROLE_ADMIN', $this->roles)) $role = 'Administrateur';
        else if(in_array('ROLE_MANAGER', $this->roles)) $role = 'Editeur';
        else $role = 'Utilisateur';
        return $role;
    }

    /**
     * Get cotisations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCotisations()
    {
        return $this->cotisations;
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

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return ($this->avancements->last()) ? $this->avancements->last()->getStatus() : '';
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getGroupList()
    {
        $list = [];
        foreach ($this->groups as $group) {
            $list[] = $group->getName();
        }
        return implode(', ', $list);
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getEtat()
    {
        return ($this->enabled) ? 'activé' : 'désactivé';
    }

    public function get($value)
    {
        $value = 'get'.ucfirst($value);
        if(in_array($value, array('getUsername', 'getEmail', 'getRole', 'getEtat', 'getGroupList'))) 
            $value = $this->$value();
        else $value = $this->profile->$value();

        return ($value instanceof \DateTime) ? $value->format('Y-m-d') : $value;
    }
}