<?php

namespace Ben\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Entity\ThreadMetadata as BaseThreadMetadata;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\Model\ParticipantInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="thread_metadata")
 */
class ThreadMetadata extends BaseThreadMetadata {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ben\MessageBundle\Entity\Thread", inversedBy="metadata")
     */
    protected $thread;

    /**
     * @ORM\ManyToOne(targetEntity="Ben\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="participant_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $participant;

    public function setThread(ThreadInterface $thread) {
        $this->thread = $thread;
    }

    public function setParticipant(ParticipantInterface $participant) {
        $this->participant = $participant;
        return $this;
    }
    public function getParticipant()
    {
        return $this->participant;
    }

}

?>
