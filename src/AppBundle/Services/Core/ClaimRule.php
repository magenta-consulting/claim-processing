<?php

namespace AppBundle\Services\Core;

use AppBundle\Entity\Claim;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class ClaimRule
{
    use ContainerAwareTrait;


    public function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            return;
        }

        $tokenStorage = $this->container->get('security.token_storage');

        if (!$token = $tokenStorage->getToken()) {
            return;
        }

        $user = $token->getUser();
        if (!is_object($user)) {
            return;
        }

        return $user;
    }

    public function getRuleForClaim(Claim $claim){
        $position = $this->getUser()->getLoginWithPosition();
        $company = $position->getCompany();
        $costCentre = $position->getCostCentre();
        $region = $position->getRegion();
        $branch = $position->getBranch();
        $department = $position->getDepartment();
        $section = $position->getSection();
        $employeeType = $position->getEmployeeType();

        $index =[
                'companyGetRule',
                'costCentre',
                'employeeType',
                'region',
                'branch',
                'department',
                'section',
        ];
        $filter1 = [
            'companyGetRule' => $company,
            'costCentre' => $costCentre,
            'employeeType'=>$employeeType,
            'region' => $region,
            'branch' => $branch,
            'department' => $department,
            'section' => $section,
        ];

        $claimType = $claim->getClaimType();
        $claimCategory = $claim->getClaimCategory();
        $filter2 = [
            'claimType'=>$claimType,
            'claimCategory'=>$claimCategory
        ];

        $em = $this->container->get('doctrine')->getManager();

        for($i = count($filter1)-1 ;$i >=0 ;$i--){
            $filter = array_merge($filter1,$filter2);
            $rule = $em->getRepository('AppBundle\Entity\Category')->findOneBy($filter);
            if($rule){
                return $rule->getClaimLimitDescription();
            }
            unset($filter1[$index[$i]]);
        }
        return null;
    }
    public function getChecker(Claim $claim)
    {
        $position = $this->getUser()->getLoginWithPosition();
        $company = $position->getCompany();
        $costCentre = $position->getCostCentre();
        $region = $position->getRegion();
        $branch = $position->getBranch();
        $department = $position->getDepartment();
        $section = $position->getSection();

        $index =[
            'companySetupChecker',
            'costCentre',
            'region',
            'branch',
            'department',
            'section',
        ];
        $filter = [
            'companySetupChecker' => $company,
            'costCentre' => $costCentre,
            'region' => $region,
            'branch' => $branch,
            'department' => $department,
            'section' => $section,
        ];
        $em = $this->container->get('doctrine')->getManager();

        for($i = count($filter)-1 ;$i >=0 ;$i--){
            $checker = $em->getRepository('AppBundle\Entity\Checker')->findOneBy($filter);
            if($checker){
                return $checker;
            }
            unset($filter[$index[$i]]);
        }
        return null;
    }

    public function getApprover(Claim $claim)
    {
        $position = $this->getUser()->getLoginWithPosition();
        $company = $position->getCompany();
        $costCentre = $position->getCostCentre();
        $region = $position->getRegion();
        $branch = $position->getBranch();
        $department = $position->getDepartment();
        $section = $position->getSection();
        $employeeType = $position->getEmployeeType();

        $index =[
            'companySetupApproval',
            'costCentre',
            'employeeType',
            'region',
            'branch',
            'department',
            'section',
        ];
        $filter = [
            'companySetupApproval' => $company,
            'costCentre' => $costCentre,
            'employeeType'=>$employeeType,
            'region' => $region,
            'branch' => $branch,
            'department' => $department,
            'section' => $section,
        ];
        $em = $this->container->get('doctrine')->getManager();

        for($i = count($filter)-1 ;$i >=0 ;$i--){
            $approver = $em->getRepository('AppBundle\Entity\ApprovalAmountPolicies')->findOneBy($filter);
            if($approver){
                return $approver;
            }
            unset($filter[$index[$i]]);
        }
        return null;
    }



}
