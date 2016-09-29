<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SwitchUserController extends Controller
{
    public function indexAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $positions = $user->getPositions();
        if($request->isMethod('POST')){
            $company = $em->getRepository('AppBundle\Entity\Company')->find($request->get('company'));
            $position = $em->getRepository('AppBundle\Entity\Position')->findOneBy(['company'=>$company,'user'=>$user]);
            $user->setCompany($company);
            $user->setRoles([$position->getRoles()]);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('sonata_admin_dashboard');
        }
        return $this->render('@App/SwitchUser/index.html.twig',['positions'=>$positions]);
    }
}