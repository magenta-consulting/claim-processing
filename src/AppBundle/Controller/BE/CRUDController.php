<?php

namespace AppBundle\Controller\BE;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

class CRUDController extends Controller
{
    public function historyCurrencyAction()
    {
        $id = $this->admin->getRequest()->get('id');

        $em = $this->getDoctrine()->getManager();
        $histories = $em->getRepository('AppBundle:CurrencyExchangeHistory')->findBy(
            ['company'=>$this->getUser()->getCompany()->getId(),'currencyExchange'=>$id],
            ['createdAt'=>'desc']
        );
        return $this->render('AppBundle:SonataAdmin/CustomActions:history-currency.html.twig',['histories'=>$histories]);

    }
}