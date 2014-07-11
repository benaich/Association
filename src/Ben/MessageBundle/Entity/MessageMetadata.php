<?php

namespace Ben\MessageBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    use FOS\MessageBundle\Entity\MessageMetadata as BaseMessageMetadata;
    use FOS\MessageBundle\Model\MessageInterface;
    use FOS\MessageBundle\Model\ParticipantInterface;

    /**
     * @ORM\Entity
     * @ORM\Table(name="message_metadata")
     */
    class MessageMetadata extends BaseMessageMetadata
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\generatedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @ORM\ManyToOne(targetEntity="Ben\MessageBundle\Entity\Message", inversedBy="metadata")
         */
        protected $message;

        /**
         * @ORM\ManyToOne(targetEntity="Ben\UserBundle\Entity\User")
         * @ORM\JoinColumn(name="participant_id", referencedColumnName="id", onDelete="CASCADE")
         */
        protected $participant;

        public function setMessage(MessageInterface $message) {
    	    $this->message = $message;
    	    return $this;
    	}
    
    	public function setParticipant(ParticipantInterface $participant) {
    		$this->participant = $participant;
    		return $this;
    	}

    }
?>
