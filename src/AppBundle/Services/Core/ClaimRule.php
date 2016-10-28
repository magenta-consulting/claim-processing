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


    public function getClaimTypeDefault()
    {
        $position = $this->getPosition();
        $company = $this->getCompany();
        $clientCompany = $company->getParent() ? $company->getParent() : $company;
        $em = $this->container->get('doctrine')->getManager();
        $claimType = $em->getRepository('AppBundle\Entity\ClaimType')->findOneBy(['isDefault' => true, 'company' => $clientCompany]);
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

    public function getListClaimPeriodForFilter()
    {
        $position = $this->getPosition();
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select('claim');
        $qb->from('AppBundle:Claim', 'claim');
        $qb->join('claim.checker', 'checker');
        $qb->where('checker.checker = :position');
        $qb->orderBy('claim.createdAt', 'DESC');
        $qb->setParameter('position', $position);
        $claims = $qb->getQuery()->getResult();

        $listPeriod = [];
        foreach ($claims as $claim) {
            $listPeriod[$claim->getPeriodFrom()->format('d M Y') . ' - ' . $claim->getPeriodTo()->format('d M Y')] = $claim->getPeriodFrom()->format('Y-m-d');
        }
        return $listPeriod;
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
            ->setParameter('position', $this->getPosition())
            ->setParameter('claimType', $claim->getClaimType())
            ->setParameter('claimCategory', $claim->getClaimCategory())
            ->setParameter('periodFrom', $periodFrom->format('Y-m-d'))
            ->setParameter('periodTo', $periodTo->format('Y-m-d'))
            ->setParameter('statusList', [Claim::STATUS_DRAFT, Claim::STATUS_CHECKER_REJECTED, Claim::STATUS_APPROVER_REJECTED])
            ->getQuery()
            ->getResult();

        $limitAmount = $this->getLimitAmount($claim);
        if (!$limitAmount) {
            return false;
        }
        $totalAmount = $claim->getClaimAmount();
        foreach ($claims as $claim) {
            $totalAmount += $claim->getClaimAmount();
        }
        if ($totalAmount > $limitAmount) {
            return true;
        }
        return false;
    }

    public function getNumberClaim($position, $positionChecker)
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select($qb->expr()->count('claim.id'));
        $qb->from('AppBundle:Claim', 'claim');
        $qb->join('claim.checker', 'checker');
        $qb->where('claim.position = :position');
        $qb->andWhere('checker.checker = :positionChecker');
        $qb->andWhere('claim.status <> :status');
        $qb->setParameter('status', Claim::STATUS_DRAFT);
        $qb->setParameter('position', $position);
        $qb->setParameter('positionChecker', $positionChecker);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function isShowMenuForChecker($position)
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select($qb->expr()->count('claim.id'));
        $qb->from('AppBundle:Claim', 'claim');
        $qb->join('claim.checker', 'checker');
        $qb->where('checker.checker = :position');
        $qb->andWhere('claim.status <> :status');
        $qb->setParameter('status', Claim::STATUS_DRAFT);
        $qb->setParameter('position', $position);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getLimitAmount(Claim $claim)
    {
        $em = $this->container->get('doctrine')->getManager();
        $limitRule = $em->getRepository('AppBundle\Entity\LimitRule')->findOneBy([
            'claimType' => $claim->getClaimType(),
            'claimCategory' => $claim->getClaimCategory()
        ]);
        if (!$limitRule) {
            return null;
        }
        $limitRuleEmployeeGroup = $em->getRepository('AppBundle\Entity\LimitRuleEmployeeGroup')->findOneBy([
            'limitRule' => $limitRule,
            'employeeGroup' => $this->getPosition()->getEmployeeGroup()
        ]);
        if (!$limitRuleEmployeeGroup) {
            return null;
        }
        return $limitRuleEmployeeGroup->getClaimLimit();
    }


    public function getChecker(Claim $claim)
    {
        $expr = new Expr();
        $em = $this->container->get('doctrine')->getManager();
        return $em->createQueryBuilder()
            ->select('checker')
            ->from('AppBundle\Entity\Checker', 'checker')
            ->join('checker.checkerEmployeeGroups', 'checkerEmployeeGroup')
            ->join('checkerEmployeeGroup.employeeGroup', 'employeeGroup')
            ->where($expr->eq('employeeGroup', ':employeeGroup'))
            ->setParameter('employeeGroup', $this->getPosition()->getEmployeeGroup())
            ->getQuery()->getOneOrNullResult();
    }

    public function getApprover(Claim $claim)
    {
        $expr = new Expr();
        $em = $this->container->get('doctrine')->getManager();
        return $em->createQueryBuilder()
            ->select('approvalAmountPolicies')
            ->from('AppBundle\Entity\ApprovalAmountPolicies', 'approvalAmountPolicies')
            ->join('approvalAmountPolicies.approvalAmountPoliciesEmployeeGroups', 'approvalAmountPoliciesEmployeeGroup')
            ->join('approvalAmountPoliciesEmployeeGroup.employeeGroup', 'employeeGroup')
            ->where($expr->eq('employeeGroup', ':employeeGroup'))
            ->setParameter('employeeGroup', $this->getPosition()->getEmployeeGroup())
            ->getQuery()->getOneOrNullResult();
    }


}
