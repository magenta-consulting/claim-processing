<?php

namespace AppBundle\Services\Core;

use AppBundle\Entity\Core\EmailTemplate;
use Symfony\Component\DependencyInjection\Container;
use AppBundle\Entity\Core\Template;

class EmailSender
{

    private $mailer;
    private $twig;
    private $container;

    function __construct($mailer, $twig, Container $container)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->container = $container;
    }

    public function prepareMessageContent($template, array $vars)
    {
        $keyBody = 'body';
        $keySubject = 'subject';

        $templateSubject = $template->getSubject();
        $templateBody = $template->getBody();

        $templates = array($keySubject => $templateSubject, $keyBody => $templateBody);
        $env = new \Twig_Environment(new \Twig_Loader_Array($templates));
        $subject = $env->render($keySubject, $vars);
        $content = $env->render($keyBody, $vars);
        return array('subject' => $subject, 'content' => $content);
    }

    public function sendEmailContact($data)
    {
        $em = $this->container->get('doctrine')->getManager();
        $vars = array(
            'TITLE' => $data['title'],
            'NAME_USER' => $data['name_user'],
            'MESSAGE' => $data['message'],
        );
        $temlate = $em->getRepository('AppBundle:Core\EmailTemplate')->findOneBy(array('code' => EmailTemplate::TYPE_CONTACT));
        $temlatePrepare = $this->prepareMessageContent($temlate, $vars);
        $emailTo = $this->container->getParameter('email_contact');
        $message = \Swift_Message::newInstance()
            ->setSubject($temlatePrepare['subject'])
            ->setFrom('noreply@zonpage.com')
            ->setTo($emailTo)
            ->setContentType("text/html")
            ->setBody($temlatePrepare['content']);
        $mailer = $this->mailer;
        if (!$mailer->send($message)) {

        }
        $spool = $mailer->getTransport()->getSpool();
        $transport = $this->container->get('swiftmailer.transport.real');
        $spool->flushQueue($transport);
    }

//    public function sendEmailContact($emailTo,$booking,$path)
//    {
//        $em = $this->container->get('doctrine')->getManager();
//
//        $message = \Swift_Message::newInstance()
//                ->setSubject('Receipt Booking From ZONPAGE')
//                ->setFrom('noreply@zonpage.com')
//                ->setTo($emailTo)
//                ->setContentType("text/html")
//                ->setBody('Hi,<br>Here is your receipt.')
//                ->attach(\Swift_Attachment::fromPath($path));
//        $mailer = $this->mailer;
//        if (!$mailer->send($message)) {
//
//        }
//        $spool = $mailer->getTransport()->getSpool();
//        $transport = $this->container->get('swiftmailer.transport.real');
//        $spool->flushQueue($transport);
//    }
    public function sendEmailOfferPlot($emailTo, $data)
    {
        $em = $this->container->get('doctrine')->getManager();
        $vars = array(
            'SPACE_NAME' => $data['space_name'],
            'HOST_NAME' => $data['host_name'],
            'USER_NAME' => $data['user_name'],
            'LINK_BOOKING' => $data['url'],
        );
        $temlate = $em->getRepository('AppBundle:Core\EmailTemplate')->findOneBy(array('code' => EmailTemplate::TYPE_OFFER_PLOT));
        $temlatePrepare = $this->prepareMessageContent($temlate, $vars);
        $message = \Swift_Message::newInstance()
            ->setSubject($temlatePrepare['subject'])
            ->setFrom('noreply@zonpage.com')
            ->setTo($emailTo)
            ->setContentType("text/html")
            ->setBody($temlatePrepare['content']);
        $mailer = $this->mailer;
        if (!$mailer->send($message)) {

        }
        $spool = $mailer->getTransport()->getSpool();
        $transport = $this->container->get('swiftmailer.transport.real');
        $spool->flushQueue($transport);
    }
    public function sendEmailApproveBooking($emailTo, $data)
    {
        $em = $this->container->get('doctrine')->getManager();
        $vars = array(
            'SPACE_NAME' => $data['space_name'],
            'HOST_NAME' => $data['host_name'],
            'USER_NAME' => $data['user_name'],
            'LINK_BOOKING' => $data['url'],
        );
        $temlate = $em->getRepository('AppBundle:Core\EmailTemplate')->findOneBy(array('code' => EmailTemplate::TYPE_APPROVE_BOOKING));
        $temlatePrepare = $this->prepareMessageContent($temlate, $vars);

        //for testttttttttttttttttttttttt
//        $emailTo = $this->container->getParameter('email_contact');


        $message = \Swift_Message::newInstance()
            ->setSubject($temlatePrepare['subject'])
            ->setFrom('noreply@zonpage.com')
            ->setTo($emailTo)
            ->setContentType("text/html")
            ->setBody($temlatePrepare['content']);
        $mailer = $this->mailer;
        if (!$mailer->send($message)) {

        }
        $spool = $mailer->getTransport()->getSpool();
        $transport = $this->container->get('swiftmailer.transport.real');
        $spool->flushQueue($transport);
    }
    public function sendEmailInbox($emailTo, $data)
    {
        $em = $this->container->get('doctrine')->getManager();
        $vars = array(
            'NAME_USER_TO' => $data['name_user_to'],
            'NAME_USER_FROM' => $data['name_user_from'],
            'MESSAGE' => $data['message'],
        );
        $temlate = $em->getRepository('AppBundle:Core\EmailTemplate')->findOneBy(array('code' => EmailTemplate::TYPE_INBOX));
        $temlatePrepare = $this->prepareMessageContent($temlate, $vars);

        //for testttttttttttttttttttttttt
        $emailTo = $this->container->getParameter('email_contact');


        $message = \Swift_Message::newInstance()
            ->setSubject($temlatePrepare['subject'])
            ->setFrom('noreply@zonpage.com')
            ->setTo($emailTo)
            ->setContentType("text/html")
            ->setBody($temlatePrepare['content']);
        $mailer = $this->mailer;
        if (!$mailer->send($message)) {

        }
        $spool = $mailer->getTransport()->getSpool();
        $transport = $this->container->get('swiftmailer.transport.real');
        $spool->flushQueue($transport);
    }


}
