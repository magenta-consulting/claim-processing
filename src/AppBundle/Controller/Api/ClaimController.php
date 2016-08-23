<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Claim;
use AppBundle\Entity\Company;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Doctrine\Common\Util\Debug;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;


/**
 * @RouteResource("Claim")
 */
class ClaimController extends FOSRestController
{

    public function getAction(Company $company,Claim $claim)
    {
        return $this->setSuccessResponse($claim);
    }

    public function cgetAction(Request $request, Company $company)
    {
        $em = $this->getDoctrine()->getManager();
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);
        $order = null;
        $claims = $em->getRepository('AppBundle:Claim')->findBy(['company'=>$company], $order, $limit, $offset);
        return $this->setSuccessResponse($claims);
    }

    public function postAction(Request $request, Company $company)
    {

    }

    public function getClaimTypesAction(Request $request, Company $company)
    {
        $em = $this->getDoctrine()->getManager();
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);
        $order = null;
        $claims = $em->getRepository('AppBundle:ClaimType')->findBy(['company'=>$company], $order, $limit, $offset);
        $data = [];
        foreach ($claims as $claim){
            $data[$claim->getId()] = $claim->getCode();
        }
        return $this->setSuccessResponse($data);
    }

    public function getClaimCategoriesAction(Request $request, Company $company)
    {
        $em = $this->getDoctrine()->getManager();
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);
        $order = null;
        $categories = $em->getRepository('AppBundle:ClaimCategory')->findBy(['company'=>$company], $order, $limit, $offset);
        $data = [];
        foreach ($categories as $category){
            $data[$category->getId()] = $category->getCode();
        }
        return $this->setSuccessResponse($data);
    }

    public function getTaxRatesAction(Request $request, Company $company)
    {
        $em = $this->getDoctrine()->getManager();
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);
        $order = null;
        $taxRates = $em->getRepository('AppBundle:TaxRate')->findBy(['company'=>$company], $order, $limit, $offset);
        $data = [];
        foreach ($taxRates as $taxRate){
            $data[$taxRate->getId()] = $taxRate->getCode();
        }
        return $this->setSuccessResponse($data);
    }
    public function getCurrencyExchangeAction(Request $request, Company $company)
    {
        $em = $this->getDoctrine()->getManager();
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);
        $order = null;
        $currencyExchanges = $em->getRepository('AppBundle:CurrencyExchange')->findBy(['company'=>$company], $order, $limit, $offset);
        $data = [];
        foreach ($currencyExchanges as $currencyExchange){
            $data[$currencyExchange->getId()] = $currencyExchange->getCode();
        }
        return $this->setSuccessResponse($data);
    }
    /**
     * Set Error Response to view
     * @param String $message
     *
     * @return viewHandle
     */
    public function setErrorResponse($message = '')
    {
        $dataResponse = array(
            'status' => false,
            'message' => $message
        );
        $view = $this->view($dataResponse, Response::HTTP_BAD_REQUEST);
        return $this->handleView($view);
    }

    /**
     * Set Success Response to view
     * @param String $message
     *
     * @return viewHandle
     */
    public function setSuccessResponse($data, $message = '')
    {
        $dataResponse = array(
            'status' => true,
            'message' => $message,
            'data' => $data
        );
        $view = $this->view($dataResponse, Response::HTTP_OK);
        return $this->handleView($view);

    }
    public function optionsAction()
    {
        $response = new Response();
        $response->headers->set('Allow', 'OPTIONS, GET, PATCH, POST, PUT, DELETE');
        return $response;
    }


}
