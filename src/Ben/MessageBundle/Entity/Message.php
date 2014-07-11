<?php

namespace Ben\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\MessageBundle\Entity\Message as BaseMessage;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\Model\ParticipantInterface;
use FOS\MessageBundle\Model\MessageMetadata as ModelMessageMetadata;

/**
 * @ORM\Entity
 * @ORM\Table(name="message")
 */
class Message extends BaseMessage {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ben\MessageBundle\Entity\Thread", inversedBy="messages")
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id")
     */
    protected $thread;

    /**
     * @ORM\ManyToOne(targetEntity="Ben\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $sender;

    /**
     * @ORM\OneToMany(targetEntity="Ben\MessageBundle\Entity\MessageMetadata", mappedBy="message", cascade={"all"})
     */
    protected $metadata;

    public function __construct() {
        parent::__construct();

        $this->metadata = new ArrayCollection();
    }

    public function setThread(ThreadInterface $thread) {
        $this->thread = $thread;
        return $this;
    }

    public function setSender(ParticipantInterface $sender) {
        $this->sender = $sender;
        return $this;
    }

    public function addMetadata(ModelMessageMetadata $meta) {
        $meta->setMessage($this);
        parent::addMetadata($meta);
    }
    
    public function getExcerpt($count = 60, $more = null ) {
		if ( null === $more ) $more = '';
		$str = $this->strip_all_tags( $this->body, true );
		$excerpt = mb_substr( $str, 0, $count );
		// remove part of an entity at the end
		$excerpt = preg_replace( '/&[^;\s]{0,6}$/', '', $excerpt );
		if ( $str != $excerpt )
			$excerpt = trim( $excerpt ) . $more;
		return $excerpt;
	}
	public function strip_all_tags($string, $remove_breaks = false) {
		$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
		$string = strip_tags($string);

		if ( $remove_breaks )
			$string = preg_replace('/[\r\n\t ]+/', ' ', $string);

		return trim( $string );
	}
}

?>
