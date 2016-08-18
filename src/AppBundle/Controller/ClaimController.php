<?php

namespace AppBundle\Controller;

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
    public function getAction(Request $request, Company $company, Claim $claim)
    {
        return $this->setSuccessResponse($claim);
    }

    public function cgetAction(Request $request, Company $company)
    {
        $em = $this->getDoctrine()->getManager();
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);
        $order = null;
        $claims = $em->getRepository('AppBundle:Claim')->findBy([], $order, $limit, $offset);
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
        $claims = $em->getRepository('AppBundle:ClaimType')->findBy([], $order, $limit, $offset);
        return $this->setSuccessResponse($claims);
    }

    public function getClaimCategoriesAction(Request $request, Company $company)
    {
        $em = $this->getDoctrine()->getManager();
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);
        $order = null;
        $claims = $em->getRepository('AppBundle:ClaimCategory')->findBy([], $order, $limit, $offset);
        return $this->setSuccessResponse($claims);
    }

    public function getTaxRatesAction(Request $request, Company $company)
    {
        $em = $this->getDoctrine()->getManager();
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);
        $order = null;
        $claims = $em->getRepository('AppBundle:TaxRate')->findBy([], $order, $limit, $offset);
        return $this->setSuccessResponse($claims);
    }


}
