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
use AppBundle\Entity\User;

/**
 * @RouteResource("Company")
 */

class CompanyController extends FOSRestController
{



    public function loginAction()
    {
        $this->redirectToRoute();
        return $this->setSuccessResponse(null,'Login Success');
    }
    public function getAction(Company $company)
    {
        return $this->setSuccessResponse($company);
    }
    public function getUserAction($username)
    {
        $user = $em = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(['username'=>$username]);
        return $this->setSuccessResponse($user);
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
