<?php

namespace Ben\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ben\UserBundle\Entity\profile
 *
 * @ORM\Table(name="profile")
 * @ORM\Entity(repositoryClass="Ben\UserBundle\Entity\profileRepository")
 * @UniqueEntity(fields="cin", message="user.cin.already_used")
 * @UniqueEntity(fields="barcode", message="user.barcode.already_used")
 */
class profile
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
     * @var string $family_name
     *
     * @ORM\Column(name="family_name", type="string", length=255, nullable=true)
     * @Assert\MaxLength(limit=20)
     * @Assert\MinLength(limit=2)
     * @Assert\NotBlank()
     */
    private $family_name;

    /**
     * @var string $first_name
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $first_name;

    /**
     * @var string $barcode
     *
     * @ORM\Column(name="barcode", type="string")
     */
    private $barcode;

    /**
     * @var string $cin
     *
     * @ORM\Column(name="cin", type="string", length=255, nullable=true)
     */
    private $cin;


    /**
     * @var \DateTime $birthday
     *
     * @ORM\Column(name="birthday", type="date")
     */
    private $birthday;

    /**
     * @var string $gender
     *
     * @ORM\Column(name="gender", type="string", length=255, nullable=true)
     */
    private $gender;

    /**
     * @var string $address
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var integer $post_code
     *
     * @ORM\Column(name="post_code", type="integer", nullable=true)
     */
    private $post_code;

    /**
     * @var string $city
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string $contry
     *
     * @ORM\Column(name="contry", type="string", length=255, nullable=true)
     */
    private $contry;
    
    /**
     * @var string $job
     *
     * @ORM\Column(name="job", type="string", length=255, nullable=true)
     */
    private $job;
    
    /**
     * @var string $tel
     *
     * @ORM\Column(name="tel", type="string", length=255, nullable=true)
     */
    private $tel;
    
    /**
     * @var string $gsm
     *
     * @ORM\Column(name="gsm", type="string", length=255, nullable=true)
     */
    private $gsm;

    /**
     * @var string
     *
     * @ORM\Column(name="diplome", type="string", length=255, nullable=true)
     */
    private $diplome;

    /**
     * @var string
     *
     * @ORM\Column(name="expertise", type="string", length=255, nullable=true)
     */
    private $expertise;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string $website
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @var string $facebook
     *
     * @ORM\Column(name="facebook", type="string", length=255, nullable=true)
     */
    private $facebook;

    /**
     * @var string $google
     *
     * @ORM\Column(name="google", type="string", length=255, nullable=true)
     */
    private $google;

    /**
     * @var string $twitter
     *
     * @ORM\Column(name="twitter", type="string", length=255, nullable=true)
     */
    private $twitter;

    /**
     * @var integer $frequence
     *
     * @ORM\Column(name="frequence", type="integer", nullable=true)
     */
    private $frequence;

    /**
     * @var string $method
     *
     * @ORM\Column(name="method", type="string", length=255, nullable=true)
     */
    private $method;

    /**
     * @var float $montant
     *
     * @ORM\Column(name="montant", type="float")
     */
    private $montant;

    /**
     * @var boolean $archived
     *
     * @ORM\Column(name="archived", type="boolean")
     */
    private $archived;

    /**
     * @var string $cause
     *
     * @ORM\Column(name="cause", type="text", nullable=true)
     */
    private $cause;
    
    /**
    * @ORM\OneToOne(targetEntity="Ben\AssociationBundle\Entity\image", cascade={"remove", "persist"})
    * @Assert\Valid()
    */
    private $image;
    
    /************ Le constructeur ************/
    
    public function __construct()
    {
        $this->birthday =  new \DateTime;
        $this->barcode =  str_pad(mt_rand(0, 9999999999), 6, '0', STR_PAD_LEFT);
        $this->image = new \Ben\AssociationBundle\Entity\image();
        $this->image->setPath("anonymous.jpg");
        $this->archived =  0;
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
     * Get fullname
     *
     * @return string 
     */
    public function getFullName()
    {
        return $this->family_name.' '.$this->first_name;
    }

    /**
     * Set family_name
     *
     * @param string $familyName
     * @return profile
     */
    public function setFamilyName($familyName)
    {
        $this->family_name = $familyName;
    
        return $this;
    }

    /**
     * Get family_name
     *
     * @return string 
     */
    public function getFamilyName()
    {
        return $this->family_name;
    }

    /**
     * Set first_name
     *
     * @param string $firstName
     * @return profile
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;
    
        return $this;
    }

    /**
     * Get first_name
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set barcode
     *
     * @param integer $barcode
     * @return profile
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    
        return $this;
    }

    /**
     * Get barcode
     *
     * @return string 
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * Set cin
     *
     * @param string $cin
     * @return profile
     */
    public function setCin($cin)
    {
        $this->cin = $cin;
    
        return $this;
    }

    /**
     * Get cin
     *
     * @return string 
     */
    public function getCin()
    {
        return $this->cin;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return profile
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    
        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return profile
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    
        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return profile
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
     * @return profile
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
     * Set job
     *
     * @param string $job
     * @return profile
     */
    public function setJob($job)
    {
        $this->job = $job;
    
        return $this;
    }

    /**
     * Get job
     *
     * @return string 
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return profile
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
     * Set website
     *
     * @param string $website
     * @return profile
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    
        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set facebook
     *
     * @param string $facebook
     * @return profile
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    
        return $this;
    }

    /**
     * Get facebook
     *
     * @return string 
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set google
     *
     * @param string $google
     * @return profile
     */
    public function setGoogle($google)
    {
        $this->google = $google;
    
        return $this;
    }

    /**
     * Get google
     *
     * @return string 
     */
    public function getGoogle()
    {
        return $this->google;
    }

    /**
     * Set twitter
     *
     * @param string $twitter
     * @return profile
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;
    
        return $this;
    }

    /**
     * Get twitter
     *
     * @return string 
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set image
     *
     * @param \Ben\AssociationBundle\Entity\image $image
     * @return profile
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
     * Set post_code
     *
     * @param integer $post_code
     * @return profile
     */
    public function setPostCode($post_code)
    {
        $this->post_code = $post_code;
    
        return $this;
    }

    /**
     * Get post_code
     *
     * @return integer 
     */
    public function getPostCode()
    {
        return $this->post_code;
    }

    /**
     * Set contry
     *
     * @param string $contry
     * @return profile
     */
    public function setContry($contry)
    {
        $this->contry = $contry;
    
        return $this;
    }

    /**
     * Get contry
     *
     * @return string 
     */
    public function getContry()
    {
        return $this->contry;
    }

    /**
     * Set tel
     *
     * @param string $tel
     * @return profile
     */
    public function setTel($tel)
    {
        $this->tel = $tel;
    
        return $this;
    }

    /**
     * Get tel
     *
     * @return string 
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set gsm
     *
     * @param string $gsm
     * @return profile
     */
    public function setGsm($gsm)
    {
        $this->gsm = $gsm;
    
        return $this;
    }

    /**
     * Get gsm
     *
     * @return string 
     */
    public function getGsm()
    {
        return $this->gsm;
    }

    /**
     * Set diplome
     *
     * @param string $diplome
     * @return profile
     */
    public function setDiplome($diplome)
    {
        $this->diplome = $diplome;
    
        return $this;
    }

    /**
     * Get diplome
     *
     * @return string 
     */
    public function getDiplome()
    {
        return $this->diplome;
    }

    /**
     * Set expertise
     *
     * @param string $expertise
     * @return profile
     */
    public function setExpertise($expertise)
    {
        $this->expertise = $expertise;
    
        return $this;
    }

    /**
     * Get expertise
     *
     * @return string 
     */
    public function getExpertise()
    {
        return $this->expertise;
    }

    /**
     * Set frequence
     *
     * @param integer $frequence
     * @return profile
     */
    public function setFrequence($frequence)
    {
        $this->frequence = $frequence;
    
        return $this;
    }

    /**
     * Get frequence
     *
     * @return integer 
     */
    public function getFrequence()
    {
        return $this->frequence;
    }    

    /**
     * Get frequence
     *
     * @return integer 
     */
    public function getFrequenceLabel()
    {
        $labels = array(1=>'mensuel',3=>'Trimestriel',6=>'Semestriel',12=>'Annuel');
        return (in_array($this->frequence, array_keys($labels))) ? $labels[$this->frequence] : '';
    }

    /**
     * Set method
     *
     * @param string $method
     * @return profile
     */
    public function setMethod($method)
    {
        $this->method = $method;
    
        return $this;
    }

    /**
     * Get method
     *
     * @return string 
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set montant
     *
     * @param float $montant
     * @return Cotisation
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;
    
        return $this;
    }

    /**
     * Get montant
     *
     * @return float 
     */
    public function getMontant()
    {
        return $this->montant;
    }


    /**
     * Set boolean
     *
     * @param boolean $archived
     * @return profile
     */
    public function setArchived($archived)
    {
        $this->archived = $archived;
    
        return $this;
    }

    /**
     * Get archived
     *
     * @return boolean 
     */
    public function isArchived()
    {
        return $this->archived;
    }

    /**
     * Set cause
     *
     * @param integer $cause
     * @return profile
     */
    public function setCause($cause)
    {
        $this->cause = $cause;
    
        return $this;
    }

    /**
     * Get cause
     *
     * @return string 
     */
    public function getCause()
    {
        return $this->cause;
    }
}