<?php

namespace AppBundle\Services\Core;

use AppBundle\Entity\Claim;
use Doctrine\ORM\Query\Expr;
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


    public function getClaimTypeDefault()
    {
        $position = $this->getUser()->getLoginWithPosition();
        $company = $position->getCompany();
        $clientCompany = $company->getParent() ? $company->getParent() : $company;
        $em = $this->container->get('doctrine')->getManager();
        $claimType = $em->getRepository('AppBundle\Entity\ClaimType')->findOneBy(['isDefault'=>true,'company'=>$clientCompany]);
        return $claimType;
    }

    public function getCurrentClaimPeriod($key)
    {
        $em = $this->container->get('doctrine')->getManager();
        //in the future will change with multiple cutofdate and claimable, currently just only one
        $claimType = $em->getRepository('AppBundle\Entity\ClaimType')->findOneBy([]);

        $claimPolicy = $claimType->getCompanyClaimPolicies();
        $cutOffdate = $claimPolicy->getCutOffDate();
        $currentDate = date('d');
        if ($currentDate <= $cutOffdate) {
            $periodTo = new \DateTime('NOW');
            $clone = clone $periodTo;
            $periodFrom = $clone->modify('-1 month');
        } else {
            $periodTo = new \DateTime('NOW');
            $periodTo->modify('+1 month');
            $clone = clone $periodTo;
            $periodFrom = $clone->modify('-1 month');
        }
        $periodFrom->setDate($periodFrom->format('Y'), $periodFrom->format('m'), $cutOffdate + 1);
        $periodTo->setDate($periodTo->format('Y'), $periodTo->format('m'), $cutOffdate);
        $period = ['from' => $periodFrom, 'to' => $periodTo];
        return $period[$key];
    }

    public function isExceedLimitRule(Claim $claim)
    {
        $em = $this->container->get('doctrine')->getManager();
        $periodFrom = $this->getCurrentClaimPeriod('from');
        $periodTo = $this->getCurrentClaimPeriod('to');
        $expr = new Expr();
        $claims = $em->createQueryBuilder()
            ->select('claim')
            ->from('AppBundle\Entity\Claim', 'claim')
            ->where($expr->eq('claim.position', ':position'))
            ->andWhere($expr->eq('claim.claimType', ':claimType'))
            ->andWhere($expr->eq('claim.claimCategory', ':claimCategory'))
            ->andWhere($expr->eq('claim.periodFrom', ':periodFrom'))
            ->andWhere($expr->eq('claim.periodTo', ':periodTo'))
            ->andWhere($expr->notIn('claim.status', ':statusList'))
            ->setParameter('position', $this->getUser()->getLoginWithPosition())
            ->setParameter('claimType', $claim->getClaimType())
            ->setParameter('claimCategory', $claim->getClaimCategory())
            ->setParameter('periodFrom', $periodFrom->format('Y-m-d'))
            ->setParameter('periodTo', $periodTo->format('Y-m-d'))
            ->setParameter('statusList', [Claim::STATUS_DRAFT, Claim::STATUS_CHECKER_REJECTED, Claim::STATUS_APPROVER_REJECTED])
            ->getQuery()
            ->getResult();

        $totalAmount = $claim->getClaimAmount();
        foreach ($claims as $claim) {
            $totalAmount += $claim->getClaimAmount();
        }
        if ($totalAmount > $this->getLimitAmount($claim)) {
            return true;
        }
        return false;
    }

    public function getLimitAmount(Claim $claim)
    {
        $em = $this->container->get('doctrine')->getManager();
        $limitRule = $em->getRepository('AppBundle\Entity\LimitRule')->findOneBy([
            'claimType' => $claim->getClaimType(),
            'claimCategory' => $claim->getClaimCategory()
        ]);
        $limitRuleEmployeeGroup = $em->getRepository('AppBundle\Entity\LimitRuleEmployeeGroup')->findOneBy([
            'limitRule' => $limitRule,
            'employeeGroup' => $this->getUser()->getLoginWithPosition()->getEmployeeGroup()
        ]);
        return $limitRuleEmployeeGroup->getClaimLimit();
    }

    public function getChecker(Claim $claim)
    {
        return null;
    }

    public function getApprover(Claim $claim)
    {
        return null;
    }


}
