<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    public function getTaxAmountAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $claimAmount = $request->get('claim-amount');
            $taxRateId = $request->get('tax-rate-id');
            if ($taxRateId && $claimAmount) {
                $taxAmount = $this->get('app.claim_rule')->getTaxAmount($claimAmount, $taxRateId);
                return new JsonResponse(['status' => true, 'value' => $taxAmount]);
            }
            return new JsonResponse(['status' => true, 'value' => null]);
        }
    }

    public function getClaimAmountConvertedAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $claimAmount = $request->get('claim-amount');
            $exRateId = $request->get('ex-rate-id');
            if ($exRateId && $claimAmount) {
                $claimAmountConverted = $this->get('app.claim_rule')->getClaimAmountConverted($claimAmount, $exRateId);
                return new JsonResponse(['status' => true, 'value' => $claimAmountConverted]);
            }
            return new JsonResponse(['status' => true, 'value' => null]);
        }
    }
    public function getTaxAmountConvertedAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $claimAmount = $request->get('claim-amount');
            $taxRateId = $request->get('tax-rate-id');
            $exRateId = $request->get('ex-rate-id');
            if ($taxRateId && $claimAmount && $exRateId) {
                $taxAmount = $this->get('app.claim_rule')->getTaxAmount($claimAmount, $taxRateId);
                if($taxAmount){
                    $taxAmountConverted = $this->get('app.claim_rule')->getTaxAmountConverted($taxAmount,$exRateId);
                    return new JsonResponse(['status' => true, 'value' => $taxAmountConverted]);
                }
            }
            return new JsonResponse(['status' => true, 'value' => null]);
        }
    }
}