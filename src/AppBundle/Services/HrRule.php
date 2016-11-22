<?php

namespace AppBundle\Services;

use AppBundle\Entity\Claim;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Context\ExecutionContext;
use AppBundle\Services\ClaimRule;

class HrRule extends ClaimRule
{
    public function getListClaimPeriodForFilterHr()
    {
        $expr = new Expr();
        $company = $this->getCompany();
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select('claim');
        $qb->from('AppBundle:Claim', 'claim');
        $qb->where($expr->eq('claim.company', ':company'));
        $qb->andWhere($expr->eq('claim.status', ':statusApproverApproved'));
        $qb->orderBy('claim.createdAt', 'DESC');
        $qb->setParameter('statusApproverApproved', Claim::STATUS_APPROVER_APPROVED);
        $qb->setParameter('company', $company);
        $claims = $qb->getQuery()->getResult();

        $listPeriod = [];
        foreach ($claims as $claim) {
            $listPeriod[$claim->getPeriodFrom()->format('d M Y') . ' - ' . $claim->getPeriodTo()->format('d M Y')] = $claim->getPeriodFrom()->format('Y-m-d');
        }
        return $listPeriod;
    }
    public function getListClaimPeriodForFilterHrReport()
    {
        $expr = new Expr();
        $company = $this->getCompany();
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select('claim');
        $qb->from('AppBundle:Claim', 'claim');
        $qb->where($expr->eq('claim.company', ':company'));
        $qb->andWhere($expr->eq('claim.status', ':statusProcessed'));
        $qb->orderBy('claim.createdAt', 'DESC');
        $qb->setParameter('statusProcessed', Claim::STATUS_PROCESSED);
        $qb->setParameter('company', $company);
        $claims = $qb->getQuery()->getResult();

        $listPeriod = [];
        foreach ($claims as $claim) {
            $listPeriod[$claim->getPeriodFrom()->format('d M Y') . ' - ' . $claim->getPeriodTo()->format('d M Y')] = $claim->getPeriodFrom()->format('Y-m-d');
        }
        return $listPeriod;
    }
    public function getListClaimPeriodForFilterHrReject()
    {
        $expr = new Expr();
        $company = $this->getCompany();
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select('claim');
        $qb->from('AppBundle:Claim', 'claim');
        $qb->where($expr->eq('claim.company', ':company'));
        $qb->andWhere($expr->eq('claim.status', ':statusHrRejected'));
        $qb->orderBy('claim.createdAt', 'DESC');
        $qb->setParameter('statusHrRejected', Claim::STATUS_HR_REJECTED);
        $qb->setParameter('company', $company);
        $claims = $qb->getQuery()->getResult();

        $listPeriod = [];
        foreach ($claims as $claim) {
            $listPeriod[$claim->getPeriodFrom()->format('d M Y') . ' - ' . $claim->getPeriodTo()->format('d M Y')] = $claim->getPeriodFrom()->format('Y-m-d');
        }
        return $listPeriod;
    }

    public function getTotalAmountClaimEachEmployeeForHr($position)
    {
        $expr = new Expr();
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select('SUM(claim.claimAmount)');
        $qb->from('AppBundle:Claim', 'claim');
        $qb->where('claim.position = :position');
        $qb->andWhere($expr->eq('claim.status', ':statusApproverApproved'));
        $qb->setParameter('statusApproverApproved', Claim::STATUS_APPROVER_APPROVED);
        $qb->setParameter('position', $position);

        return $qb->getQuery()->getSingleScalarResult();
    }
    public function getTotalAmountClaimEachEmployeeForHrReport($position)
    {
        $expr = new Expr();
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select('SUM(claim.claimAmount)');
        $qb->from('AppBundle:Claim', 'claim');
        $qb->where('claim.position = :position');
        $qb->andWhere($expr->eq('claim.status', ':statusHrApproved'));
        $qb->setParameter('statusHrApproved', Claim::STATUS_PROCESSED);
        $qb->setParameter('position', $position);

        return $qb->getQuery()->getSingleScalarResult();
    }
    public function getTotalAmountClaimEachEmployeeForHrReject($position)
    {
        $expr = new Expr();
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select('SUM(claim.claimAmount)');
        $qb->from('AppBundle:Claim', 'claim');
        $qb->where('claim.position = :position');
        $qb->andWhere($expr->eq('claim.status', ':statusHrRejected'));
        $qb->setParameter('statusHrRejected', Claim::STATUS_HR_REJECTED);
        $qb->setParameter('position', $position);

        return $qb->getQuery()->getSingleScalarResult();
    }


}
