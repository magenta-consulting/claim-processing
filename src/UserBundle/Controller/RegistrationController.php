<?php

namespace UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;

class RegistrationController extends BaseController
{
    public function registerSuccessAction()
    {
        return $this->render('UserBundle:Registration:success.html.twig', array());
    }
}
