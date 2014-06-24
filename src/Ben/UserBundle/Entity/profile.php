<?php

namespace Ben\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ben\UserBundle\Entity\profile
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ben\UserBundle\Entity\profileRepository")
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
     * @Assert\MaxLength(limit=20, message="Le contenu ne doit pas dépassé {{ limit }} carractere|Le contenu ne doit pas dépassé {{ limit }} carractere")
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
     * @var string $cin
     *
     * @ORM\Column(name="cin", type="string", length=255, nullable=true)
     */
    private $cin;


    /**
     * @var \DateTime $bird_day
     *
     * @ORM\Column(name="bird_day", type="date")
     */
    private $bird_day;

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
    * @ORM\OneToOne(targetEntity="Ben\AssociationBundle\Entity\image", cascade={"remove", "persist"})
    * @Assert\Valid()
    */
    private $image;
    
    /************ Le constructeur ************/
    
    public function __construct()
    {
        $this->bird_day =  new \DateTime;
        $this->image= new \Ben\AssociationBundle\Entity\image();
         $this->image->setPath("anonymous.jpg");
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
        return $this->family_name.' '.$this->family_name;
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
     * Set bird_day
     *
     * @param \DateTime $birdDay
     * @return profile
     */
    public function setBirdDay($birdDay)
    {
        $this->bird_day = $birdDay;
    
        return $this;
    }

    /**
     * Get bird_day
     *
     * @return \DateTime 
     */
    public function getBirdDay()
    {
        return $this->bird_day;
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
}