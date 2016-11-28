<?php

namespace AppBundle\Services;

use AppBundle\Entity\Claim;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Context\ExecutionContext;

class ClaimRule
{
    use ContainerAwareTrait;

    /*1 global--------------------------------------*/
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

    public function getPosition()
    {
        return $this->getUser()->getLoginWithPosition();
    }

    public function getCompany()
    {
        $company = $this->container->get('security.token_storage')->getToken()->getUser()->getCompany();
        //is admin
        if ($company === null) {

        }
        return $company;
    }

    public function getClientCompany()
    {
        //admin will return null
        $company = $this->getCompany();
        if ($company && $company->getParent()) {
            return $company->getParent();
        }
        return $company;
    }

    public function getCurrencyDefault(){
        $clientCompany = $this->getClientCompany();
        $em = $this->container->get('doctrine')->getManager();
        $currencyExchange = $em->getRepository('AppBundle\Entity\CurrencyExchange')->findOneBy(['isDefault' => true, 'company' => $clientCompany]);
        return $currencyExchange;
    }

    public function getEmployeeGroupBelongToUser($position)
    {
        $employeeGroupDescriptionStr = $position->getEmployeeGroupDescription();
        $employeeGroupDescriptionArr = explode('>', $employeeGroupDescriptionStr);
        $employeeGroupBelongUser = $this->container->get('app.util')->getResult($employeeGroupDescriptionArr);

        return $employeeGroupBelongUser;
    }

    public function getClaimTypeDefault()
    {
        $clientCompany = $this->getClientCompany();
        $em = $this->container->get('doctrine')->getManager();
        $claimType = $em->getRepository('AppBundle\Entity\ClaimType')->findOneBy(['isDefault' => true, 'company' => $clientCompany]);
        return $claimType;
    }

    public function getCurrentClaimPeriod($key)
    {
        $em = $this->container->get('doctrine')->getManager();
        //in the future will change with multiple cutofdate and claimable, currently just only one
        $claimPolicy = $em->getRepository('AppBundle\Entity\CompanyClaimPolicies')->findOneBy(['company' => $this->getClientCompany()]);

        if ($claimPolicy) {
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
        return null;
    }

    public function getPayCode($claim){
        $em = $this->container->get('doctrine')->getManager();
        $limitRule = $em->getRepository('AppBundle\Entity\LimitRule')->findOneBy([
            'claimType' => $claim->getClaimType(),
            'claimCategory' => $claim->getClaimCategory()
        ]);
        if (!$limitRule) {
            return null;
        }
        return $limitRule->getPayCode();
    }
    public function getLimitAmount(Claim $claim, $position)
    {
        $em = $this->container->get('doctrine')->getManager();
        $limitRule = $em->getRepository('AppBundle\Entity\LimitRule')->findOneBy([
            'claimType' => $claim->getClaimType(),
            'claimCategory' => $claim->getClaimCategory()
        ]);
        if (!$limitRule) {
            return null;
        }
        //may be will have many limit amount, but the priority for more detail group
        $employeeGroupBelongToUser = $this->getEmployeeGroupBelongToUser($position);
        $expr = new Expr();
        for ($i = count($employeeGroupBelongToUser) - 1; $i >= 0; $i--) {
            $limitRuleEmployeeGroup = $em->createQueryBuilder()
                ->select('limitRuleEmployeeGroup')
                ->from('AppBundle\Entity\LimitRuleEmployeeGroup', 'limitRuleEmployeeGroup')
                ->join('limitRuleEmployeeGroup.employeeGroup', 'employeeGroup')
                ->where($expr->eq('limitRuleEmployeeGroup.limitRule', ':limitRule'))
                ->andWhere($expr->eq('employeeGroup.description', ':employeeGroup'))
                ->setParameter('limitRule', $limitRule)
                ->setParameter('employeeGroup', $employeeGroupBelongToUser[$i])
                ->getQuery()->getOneOrNullResult();
            if ($limitRuleEmployeeGroup) {
                return $limitRuleEmployeeGroup->getClaimLimit();
            }
        }
        return null;
    }

    public function isExceedLimitRule(Claim $claim, $position)
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
            ->setParameter('position', $position)
            ->setParameter('claimType', $claim->getClaimType())
            ->setParameter('claimCategory', $claim->getClaimCategory())
            ->andWhere($expr->eq('claim.periodFrom', ':periodFrom'))
            ->andWhere($expr->eq('claim.periodTo', ':periodTo'))
            ->setParameter('periodFrom', $periodFrom->format('Y-m-d'))
            ->setParameter('periodTo', $periodTo->format('Y-m-d'))
            ->getQuery()
            ->getResult();

        $limitAmount = $this->getLimitAmount($claim, $position);
        if (!$limitAmount) {
            return false;
        }
        $totalAmount = 0;
        foreach ($claims as $claim) {
            $totalAmount += $claim->getClaimAmountConverted();
        }
        if ($totalAmount > $limitAmount) {
            return true;
        }
        return false;
    }
    public function getDescriptionEmployeeGroup($employeeGroup)
    {
        if($employeeGroup == null){
            return null;
        }
        $description = [];
        if ($employeeGroup->getCompanyApply()) {
            $description[] = $employeeGroup->getCompanyApply()->getName();
        }
        if ($employeeGroup->getCostCentre()) {
            $description[] = $employeeGroup->getCostCentre()->getCode();
        }
        if ($employeeGroup->getDepartment()) {
            $description[] = $employeeGroup->getDepartment()->getCode();
        }
        if ($employeeGroup->getEmployeeType()) {
            $description[] = $employeeGroup->getEmployeeType()->getCode();
        }
        if (count($description)) {
            $description = implode('>', $description);
        } else {
            $description = '';
        }
        return $description;
    }

    public function getNumberRejectedClaim()
    {
        $expr = new Expr();
        $periodFrom = $this->container->get('app.claim_rule')->getCurrentClaimPeriod('from');
        $periodTo = $this->container->get('app.claim_rule')->getCurrentClaimPeriod('to');
        $em = $this->container->get('doctrine')->getManager();
        $query = $em->createQueryBuilder('claim');
        $query->select($expr->count('claim.id'));
        $query->from('AppBundle:Claim', 'claim');
        $query->andWhere(
            $expr->eq('claim.position', ':position')
        );
        $query->andWhere(
            $expr->eq('claim.periodFrom', ':periodFrom')
        );
        $query->andWhere(
            $expr->eq('claim.periodTo', ':periodTo')
        );
        $query->andWhere($expr->orX(
            $expr->eq('claim.status', ':statusCheckerRejected'),
            $expr->eq('claim.status', ':statusApproverRejected'),
            $expr->eq('claim.status', ':statusHrRejected')
        ));
        $query->setParameter('periodFrom', $periodFrom->format('Y-m-d'));
        $query->setParameter('periodTo', $periodTo->format('Y-m-d'));
        $query->setParameter('statusCheckerRejected', Claim::STATUS_CHECKER_REJECTED);
        $query->setParameter('statusApproverRejected', Claim::STATUS_APPROVER_REJECTED);
        $query->setParameter('statusHrRejected', Claim::STATUS_HR_REJECTED);
        $query->setParameter('position', $this->getPosition());
        return $query->getQuery()->getSingleScalarResult();
    }

    /**--------------------------Work with currency------------------------**/
    public function getTaxAmount($claimAmount, $taxRateId)
    {
        $taxRate = $this->container->get('doctrine')->getManager()->find('AppBundle\Entity\TaxRate', $taxRateId);
        if ($taxRate) {
            $rate = $taxRate->getRate();
            $amountBeforeTax = $claimAmount / (1 + $rate / 100);
            $taxAmount = $claimAmount - $amountBeforeTax;
            return round($taxAmount, 2);
        }
        return null;
    }

    public function getExRate($exchangeRateId, $receiptDate)
    {
        $currencyExchange = $this->container->get('doctrine')->getManager()->find('AppBundle\Entity\CurrencyExchange', $exchangeRateId);
        if ($currencyExchange) {
            $criteria = Criteria::create();
            $expr = Criteria::expr();
            $criteria->orderBy(['effectiveDate' => Criteria::DESC]);
            $criteria->andWhere($expr->lte('effectiveDate', $receiptDate));
            $currencyExchangeValues = $currencyExchange->getCurrencyExchangeValues()->matching($criteria);
            if ($currencyExchangeValues->count()) {
                return $currencyExchangeValues[0]->getExRate();
            }
        }
        return null;
    }

    public function getClaimAmountConverted($claimAmount, $exchangeRateId, $receiptDate)
    {
        $exRate = $this->getExRate($exchangeRateId, $receiptDate);
        if ($exRate) {
            return round($claimAmount * $exRate, 2);
        }
        return null;
    }

    public function getTaxAmountConverted($taxAmount, $exchangeRateId, $receiptDate)
    {
        $exRate = $this->getExRate($exchangeRateId, $receiptDate);
        if ($exRate) {
            return round($taxAmount * $exRate, 2);
        }
        return null;
    }

}
