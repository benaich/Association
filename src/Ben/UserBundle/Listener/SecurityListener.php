<?php 
namespace Ben\UserBundle\Listener;
 
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher as EventDispatcher;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Listener responsible to change the redirection after a success login based on roles
 */
class SecurityListener
{
    protected $router;
    protected $security;
    protected $dispatcher;

    public function __construct(Router $router, SecurityContext $security, EventDispatcher $dispatcher)
    {
        $this->router = $router;
        $this->security = $security;
        $this->dispatcher = $dispatcher;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->dispatcher->addListener(KernelEvents::RESPONSE, array($this, 'onKernelResponse'));
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $locale = $request->get('_locale');
        $request->getSession()->set('_locale', $locale);

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $event->getResponse()->headers->set('Location', $this->router->generate('Ben_association_homepage'));    
        } else {
            $event->getResponse()->headers->set('Location', $this->router->generate('ben_profile_edit', 
                array('name'=>$this->security->getToken()->getUser()->getUsername())));
        }
    }
}