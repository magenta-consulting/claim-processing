<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    public function getTaxAmountAction(Request $request){
        if($request->isXmlHttpRequest()){
            $claimAmount = $request->get('claim-amount');
            $taxRateId = $request->get('tax-rate-id');
            if($taxRateId && $claimAmount){
                $taxRate = $this->getDoctrine()->getManager()->find('AppBundle\Entity\TaxRate',$taxRateId);
                $rate = $taxRate->getRate();
                $amountBeforeTax = $claimAmount/(1 + $rate/100);
                $taxAmount = $claimAmount - $amountBeforeTax;
                return new JsonResponse(['status'=>true,'value'=>round($taxAmount,2)]);
            }
            return new JsonResponse(['status'=>true,'value'=>null]);
        }
    }
}