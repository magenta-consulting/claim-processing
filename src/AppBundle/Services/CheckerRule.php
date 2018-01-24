<?php

namespace AppBundle\Services;

use AppBundle\Entity\Claim;
use AppBundle\Entity\Position;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Context\ExecutionContext;
use AppBundle\Services\ClaimRule;

class CheckerRule extends ClaimRule
{


    public function getChecker($position)
    {
        $expr = new Expr();
        $em = $this->getContainer()->get('doctrine')->getManager();
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
        $em = $this->getContainer()->get('doctrine')->getManager();
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

        $listPeriod = ['Show All'=>'all'];
        $from = $this->getCurrentClaimPeriod('from');
        $to = $this->getCurrentClaimPeriod('to');
        $listPeriod[$from->format('d M Y') . ' - ' . $to->format('d M Y')] = $from->format('Y-m-d');
        foreach ($claims as $claim) {
            $listPeriod[$claim->getPeriodFrom()->format('d M Y') . ' - ' . $claim->getPeriodTo()->format('d M Y')] = $claim->getPeriodFrom()->format('Y-m-d');
        }
        return $listPeriod;
    }
    public function getListClaimPeriodForFilterCheckerHistory()
    {
        $expr = new Expr();
        $position = $this->getPosition();
        $em = $this->getContainer()->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('checkerHistory');
        $qb->select('checkerHistory');
        $qb->from('AppBundle:checkerHistory', 'checkerHistory');
        $qb->join('checkerHistory.claim', 'claim');
        $qb->andWhere($expr->eq('checkerHistory.checkerPosition', ':checkerPosition'));
        $qb->orderBy('claim.createdAt', 'DESC');
        $qb->setParameter('checkerPosition', $position);
        $checkerHistories = $qb->getQuery()->getResult();

        $listPeriod = ['Show All'=>'all'];
        $from = $this->getCurrentClaimPeriod('from');
        $to = $this->getCurrentClaimPeriod('to');
        $listPeriod[$from->format('d M Y') . ' - ' . $to->format('d M Y')] = $from->format('Y-m-d');
        foreach ($checkerHistories as $checkerHistory) {
            $listPeriod[$checkerHistory->getPeriodFrom()->format('d M Y') . ' - ' . $checkerHistory->getPeriodTo()->format('d M Y')] = $checkerHistory->getPeriodFrom()->format('Y-m-d');
        }
        return $listPeriod;
    }

    public function getNumberClaimEachEmployeeForChecker($position, $positionChecker)
    {
        $expr = new Expr();
        $em = $this->getContainer()->get('doctrine')->getManager();
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
    public function getNumberClaimEachEmployeeForCheckerHistory($position, $positionChecker,$from)
    {
        $expr = new Expr();
        $em = $this->getContainer()->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('checkerHistory');
        $qb->select($qb->expr()->count('checkerHistory.id'));
        $qb->from('AppBundle:checkerHistory', 'checkerHistory');
        $qb->where('checkerHistory.position = :position');
        $qb->andWhere('checkerHistory.checkerPosition = :checkerPosition');
        $qb->setParameter('position', $position);
        $qb->setParameter('checkerPosition', $positionChecker);
        if ($from !='all') {
            $dateFilter = new  \DateTime($from);
            $qb->andWhere($expr->eq('checkerHistory.periodFrom', ':periodFrom'));
            $qb->setParameter('periodFrom', $dateFilter->format('Y-m-d'));
        }

        return $qb->getQuery()->getSingleScalarResult();
    }


    public function isShowMenuForChecker(Position $position)
    {
        if($position->isThirdParty()){
            return true;
        }
        $expr = new Expr();
        $em = $this->getContainer()->get('doctrine')->getManager();
        /** @var QueryBuilder $qb */
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
    
    public function isShowMenuForCheckerHistory(Position $position)
    {
        if($position->isThirdParty()){
            return true;
        }
        $expr = new Expr();
        $em = $this->getContainer()->get('doctrine')->getManager();
        /** @var QueryBuilder $qb */
        $qb = $em->createQueryBuilder('checkerHistory');
        $qb->select($qb->expr()->count('checkerHistory.id'));
        $qb->from('AppBundle:checkerHistory', 'checkerHistory');
        $qb->where($expr->eq('checkerHistory.checkerPosition', ':checkerPosition'));
        $qb->setParameter('checkerPosition', $position);

        return $qb->getQuery()->getSingleScalarResult();
    }

}
