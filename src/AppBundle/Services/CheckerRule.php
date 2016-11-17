<?php

namespace AppBundle\Services;

use AppBundle\Entity\Claim;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Context\ExecutionContext;
use AppBundle\Services\ClaimRule;

class CheckerRule extends ClaimRule
{


    public function getChecker($position)
    {
        $expr = new Expr();
        $em = $this->container->get('doctrine')->getManager();
        //may be will have many checker, but the priority for more detail group
        $employeeGroupBelongToUser = $this->getEmployeeGroupBelongToUser($position);
        for ($i = count($employeeGroupBelongToUser) - 1; $i >= 0; $i--) {
            $checker = $em->createQueryBuilder()
                ->select('checker')
                ->from('AppBundle\Entity\Checker', 'checker')
                ->join('checker.checkerEmployeeGroups', 'checkerEmployeeGroup')
                ->join('checkerEmployeeGroup.employeeGroup', 'employeeGroup')
                ->where($expr->eq('employeeGroup.description', ':employeeGroup'))
                ->setParameter('employeeGroup', $employeeGroupBelongToUser[$i])
                ->getQuery()->getOneOrNullResult();
            if ($checker) {
                return $checker;
            }
        }
        return null;

    }

    public function getListClaimPeriodForFilterChecker()
    {
        $expr = new Expr();
        $position = $this->getPosition();
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select('claim');
        $qb->from('AppBundle:Claim', 'claim');
        $qb->join('claim.checker', 'checker');
        $qb->where($expr->orX('checker.checker = :position', 'checker.backupChecker = :position'));
        $qb->andWhere($expr->eq('claim.status', ':statusPending'));
        $qb->orderBy('claim.createdAt', 'DESC');
        $qb->setParameter('statusPending', Claim::STATUS_PENDING);
        $qb->setParameter('position', $position);
        $claims = $qb->getQuery()->getResult();

        $listPeriod = [];
        foreach ($claims as $claim) {
            $listPeriod[$claim->getPeriodFrom()->format('d M Y') . ' - ' . $claim->getPeriodTo()->format('d M Y')] = $claim->getPeriodFrom()->format('Y-m-d');
        }
        return $listPeriod;
    }

    public function getNumberClaimEachEmployeeForChecker($position, $positionChecker)
    {
        $expr = new Expr();
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select($qb->expr()->count('claim.id'));
        $qb->from('AppBundle:Claim', 'claim');
        $qb->join('claim.checker', 'checker');
        $qb->where('claim.position = :position');
        $qb->andWhere($expr->orX('checker.checker = :positionChecker', 'checker.backupChecker = :positionChecker'));
        $qb->andWhere($expr->eq('claim.status', ':statusPending'));
        $qb->setParameter('statusPending', Claim::STATUS_PENDING);
        $qb->setParameter('position', $position);
        $qb->setParameter('positionChecker', $positionChecker);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function isShowMenuForChecker($position)
    {
        $expr = new Expr();
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select($qb->expr()->count('claim.id'));
        $qb->from('AppBundle:Claim', 'claim');
        $qb->join('claim.checker', 'checker');
        $qb->where($expr->orX('checker.checker = :position', 'checker.backupChecker = :position'));
        $qb->andWhere($expr->eq('claim.status', ':statusPending'));
        $qb->setParameter('statusPending', Claim::STATUS_PENDING);
        $qb->setParameter('position', $position);

        return $qb->getQuery()->getSingleScalarResult();
    }

}
