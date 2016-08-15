<?php

namespace AppBundle\Services\Core;

use AppBundle\Entity\Booking\Booking;
use AppBundle\Entity\Space\Space;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\Common\Collections\Criteria;

class ControllerService extends Controller
{

    //core---------------------------------
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RequestStack
     */
    public function getRequest()
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }

   


}
