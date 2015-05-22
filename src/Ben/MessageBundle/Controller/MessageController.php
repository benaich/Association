<?php

namespace Ben\MessageBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Httpfoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\MessageBundle\Provider\ProviderInterface;
use JMS\SecurityExtraBundle\Annotation\Secure;

class MessageController extends Controller
{
    /**
     * Displays the authenticated participant inbox
     *
     * @return Response
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function inboxAction()
    {
        $threads = $this->getProvider()->getInboxThreads();
         return $this->render('FOSMessageBundle:Message:inbox.html.twig', array(
            'threads' => $threads
        ));
    }

    public function unreadAction()
    {
        $threads = $this->getProvider()->getInboxThreads();
         return $this->render('FOSMessageBundle:Message:unread.html.twig', array(
            'threads' => $threads
        ));
    }

    /**
     * Displays the authenticated participant sent mails
     *
     * @return Response
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function sentAction()
    {
        $threads = $this->getProvider()->getSentThreads();

        return $this->render('FOSMessageBundle:Message:sent.html.twig', array(
            'threads' => $threads
        ));
    }

    /**
     * Displays the authenticated participant deleted threads
     *
     * @return Response
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function deletedAction()
    {
        $threads = $this->getProvider()->getDeletedThreads();

        return $this->render('FOSMessageBundle:Message:deleted.html.twig', array(
            'threads' => $threads
        ));
    }

    /**
     * Displays a thread, also allows to reply to it
     *
     * @param string $threadId the thread id
     * @return Response
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function threadAction($threadId)
    {
        $thread = $this->getProvider()->getThread($threadId);
        $form = $this->get('fos_message.reply_form.factory')->create($thread);
        $formHandler = $this->get('fos_message.reply_form.handler');

        if ($message = $formHandler->process($form)) {
            return $this->redirect($this->generateUrl('fos_message_thread_view', array(
                'threadId' => $message->getThread()->getId()
            )));
        }

        return $this->render('FOSMessageBundle:Message:thread.html.twig', array(
            'form' => $form->createView(),
            'thread' => $thread
        ));
    }

    /**
     * Create a new message thread
     *
     * @return Response
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
     public function newThreadAction()
    {
        $form = $this->container->get('fos_message.new_thread_form.factory')->create();
        $formHandler = $this->container->get('fos_message.new_thread_form.handler');

        if ($message = $formHandler->process($form)) {
            $this->get('session')->getFlashBag()->add('success', "message envoyé avec succée.");
            return $this->redirect($this->generateUrl('fos_message_thread_view', array(
            'threadId' => $message->getThread()->getId()
                    )));
        }
        return $this->render('FOSMessageBundle:Message:newThread.html.twig', array(
            'form' => $form->createView(),
            'data' => $form->getData()
        ));
    }

    /**
     * Deletes a thread
     * 
     * @param string $threadId the thread id
     * @return RedirectResponse
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function deleteAction($threadId)
    {
        $thread = $this->getProvider()->getThread($threadId);
        $this->container->get('fos_message.deleter')->markAsDeleted($thread);
        $this->container->get('fos_message.thread_manager')->saveThread($thread);

        return $this->redirect($this->generateUrl('fos_message_inbox'));
    }
    
    /**
     * Undeletes a thread
     * 
     * @param string $threadId
     * 
     * @return RedirectResponse
     */
    public function undeleteAction($threadId)
    {
        $thread = $this->getProvider()->getThread($threadId);
        $this->container->get('fos_message.deleter')->markAsUndeleted($thread);
        $this->container->get('fos_message.thread_manager')->saveThread($thread);

        return $this->redirect($this->generateUrl('fos_message_inbox'));
    }

    /**
     * Searches for messages in the inbox and sentbox
     *
     * @return Response
     * @Secure(roles="IS_AUTHENTICATED_REMEMBERED")
     */
    public function searchAction()
    {
        $query = $this->container->get('fos_message.search_query_factory')->createFromRequest();
        $threads = $this->container->get('fos_message.search_finder')->find($query);

        return $this->render('FOSMessageBundle:Message:search.html.twig', array(
            'query' => $query,
            'threads' => $threads
        ));
    }

    /**
     * Gets the provider service
     *
     * @return ProviderInterface
     */
    protected function getProvider()
    {
        return $this->container->get('fos_message.provider');
    }

}
