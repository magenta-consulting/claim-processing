<?php

namespace UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationListener implements EventSubscriberInterface
{

    private $router;
    private $container;

    public function __construct(UrlGeneratorInterface $router, $container)
    {
        $this->router = $router;
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
//            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationCompleted',
        );
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        //set response
        $url = $this->router->generate('user_registration_success');
        $event->setResponse(new RedirectResponse($url));
    }

//    public function onRegistrationCompleted(FilterUserResponseEvent $filter)
//    {
//        //sendmail
//        $user = $filter->getUser();
//        $mail = $user->getEmailCanonical();
//        $this->container->get('app.email_sender')->sendEmailForApprovalRegistration($mail);
//    }

}
