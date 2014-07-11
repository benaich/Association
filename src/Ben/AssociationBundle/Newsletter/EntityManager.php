<?php

namespace Ben\AssociationBundle\Newsletter;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Templating\EngineInterface;

class NewsletterManager
{
    protected $mailer;
    private $em;
    protected $templating;

    public function __construct(EntityManager $em, \Swift_Mailer $mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->em = $em;
    }

    public function sendnewsLetter() {
        //$nice_name = $this->getContainer()->getParameter('nice_name'); $webmaster = $this->getContainer()->getParameter('webmaster');
        $nice_name = 'feedyourbrain';
        $webmaster = 'no-replay@feedyourbrain.com';
        $entities = $this->em->getRepository('BenAssociationBundle:posts')->getNews(4);
        $users = $this->em->getRepository('BenAssociationBundle:newsletter')->findByStatus(true);
        foreach ($users as $user) {
            $message = \Swift_Message::newInstance()
                        ->setSubject($nice_name.':: la newsletter')
                        ->setFrom($webmaster)
                        ->setTo($user->getEmail())
                        ->setBody($this->templating->render('BenAssociationBundle:newsletter:email.html.twig', array(
                            'user' => $user,
                            'entities' => $entities
                            )), 'text/html');
                $this->mailer->send($message);
        }
        return 'message sent';
    }
}