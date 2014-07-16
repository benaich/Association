<?php

namespace Ben\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\MessageBundle\Entity\Thread as BaseThread;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\Model\ThreadMetadata as ModelThreadMetadata;

/**
 * @ORM\Entity
 * @ORM\Table(name="thread")
 */
class Thread extends BaseThread {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ben\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="createdBy_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $createdBy;

    /**
     * @ORM\OneToMany(targetEntity="Ben\MessageBundle\Entity\Message", mappedBy="thread")
     */
    protected $messages;

    /**
     * @ORM\OneToMany(targetEntity="Ben\MessageBundle\Entity\ThreadMetadata", mappedBy="thread", cascade={"all"})
     */
    protected $metadata;

    public function __construct() {
        parent::__construct();

        $this->messages = new ArrayCollection();
    }

    public function setCreatedBy(ParticipantInterface $participant) {
        $this->createdBy = $participant;
        return $this;
    }

    function addMessage(MessageInterface $message) {
        $this->messages->add($message);
    }

    public function addMetadata(ModelThreadMetadata $meta) {
        $meta->setThread($this);
        parent::addMetadata($meta);
    }
    public function getMetadata()
    {
        return  $this->metadata;
    }
    public function getParticipant($current_user)
    {
        $participants = $this->getParticipantsList();
        unset($participants[array_search($current_user, $participants)]);
        $participant = ($participants) ? implode(' ', $participants) : $current_user;
        return $participant;     
    }
    public function getParticipantsList()
    {
        $participants = [];
        foreach ($this->metadata as $data) {
               $participants[] = $data->getParticipant()->getUsername();
           }
        return $participants; 
    }

}

?>
