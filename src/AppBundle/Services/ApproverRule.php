<?php

namespace AppBundle\Services;

use AppBundle\Entity\ApprovalAmountPolicies;
use AppBundle\Entity\Claim;
use AppBundle\Entity\Position;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Context\ExecutionContext;
use AppBundle\Services\ClaimRule;

class ApproverRule extends ClaimRule {
	
	/**
	 * @param Position $position
	 *
	 * @return ApprovalAmountPolicies|null
	 */
	public function getApprovalAmountPolicy(Position $position) {
		$expr = new Expr();
		$em   = $this->getContainer()->get('doctrine')->getManager();
		//may be will have many approver, but the priority for more detail group
		$employeeGroupBelongToUser = $this->getEmployeeGroupBelongToUser($position);
		for($i = count($employeeGroupBelongToUser) - 1; $i >= 0; $i --) {
			$approver = $em->createQueryBuilder()
			               ->select('approvalAmountPolicies')
			               ->from('AppBundle\Entity\ApprovalAmountPolicies', 'approvalAmountPolicies')
			               ->join('approvalAmountPolicies.approvalAmountPoliciesEmployeeGroups', 'approvalAmountPoliciesEmployeeGroup')
			               ->join('approvalAmountPoliciesEmployeeGroup.employeeGroup', 'employeeGroup')
			               ->where($expr->eq('employeeGroup.description', ':employeeGroup'))
			               ->setParameter('employeeGroup', $employeeGroupBelongToUser[ $i ])
			               ->getQuery()->getOneOrNullResult();
			if($approver) {
				return $approver;
			}
		}
		
		return null;
		
	}
	
	public function assignClaimToSpecificApprover(Claim $claim, Position $position) {
		return $claim->getApproverToAssign($this->getApprovalAmountPolicy($position));
	}
	
	public function getListClaimPeriodForFilterApprover() {
		$expr     = new Expr();
		$position = $this->getPosition();
		$em       = $this->getContainer()->get('doctrine')->getManager();
		$qb       = $em->createQueryBuilder('claim');
		$qb->select('claim');
		$qb->from('AppBundle:Claim', 'claim');
		$qb->where($expr->orX('claim.approverEmployee = :position', 'claim.approverBackupEmployee = :position'));
		$qb->andWhere($expr->eq('claim.status', ':statusCheckerApproved'));
		$qb->orderBy('claim.createdAt', 'DESC');
		$qb->setParameter('statusCheckerApproved', Claim::STATUS_CHECKER_APPROVED);
		$qb->setParameter('position', $position);
		$claims = $qb->getQuery()->getResult();
		
		$listPeriod                                                          = [ 'Show All' => 'all' ];
		$from                                                                = $this->getCurrentClaimPeriod('from');
		$to                                                                  = $this->getCurrentClaimPeriod('to');
		$listPeriod[ $from->format('d M Y') . ' - ' . $to->format('d M Y') ] = $from->format('Y-m-d');
		foreach($claims as $claim) {
			$listPeriod[ $claim->getPeriodFrom()->format('d M Y') . ' - ' . $claim->getPeriodTo()->format('d M Y') ] = $claim->getPeriodFrom()->format('Y-m-d');
		}
		
		return $listPeriod;
	}
	
	public function getListClaimPeriodForFilterApproverHistory() {
		$expr     = new Expr();
		$position = $this->getPosition();
		$em       = $this->getContainer()->get('doctrine')->getManager();
		$qb       = $em->createQueryBuilder('approverHistory');
		$qb->select('approverHistory');
		$qb->from('AppBundle:approverHistory', 'approverHistory');
		$qb->join('approverHistory.claim', 'claim');
		$qb->andWhere($expr->eq('approverHistory.approverPosition', ':approverPosition'));
		$qb->orderBy('claim.createdAt', 'DESC');
		$qb->setParameter('approverPosition', $position);
		$approverHistories = $qb->getQuery()->getResult();
		
		$listPeriod                                                          = [ 'Show All' => 'all' ];
		$from                                                                = $this->getCurrentClaimPeriod('from');
		$to                                                                  = $this->getCurrentClaimPeriod('to');
		$listPeriod[ $from->format('d M Y') . ' - ' . $to->format('d M Y') ] = $from->format('Y-m-d');
		foreach($approverHistories as $approverHistory) {
			$listPeriod[ $approverHistory->getPeriodFrom()->format('d M Y') . ' - ' . $approverHistory->getPeriodTo()->format('d M Y') ] = $approverHistory->getPeriodFrom()->format('Y-m-d');
		}
		
		return $listPeriod;
	}
	
	public function getNumberClaimEachEmployeeForApprover($position, $positionApprover) {
		$expr = new Expr();
		$em   = $this->getContainer()->get('doctrine')->getManager();
		$qb   = $em->createQueryBuilder('claim');
		$qb->select($qb->expr()->count('claim.id'));
		$qb->from('AppBundle:Claim', 'claim');
		$qb->where('claim.position = :position');
		$qb->andWhere($expr->orX('claim.approverEmployee = :positionApprover', 'claim.approverBackupEmployee = :positionApprover'));
		$qb->andWhere($expr->eq('claim.status', ':statusCheckerApproved'));
		$qb->setParameter('statusCheckerApproved', Claim::STATUS_CHECKER_APPROVED);
		$qb->setParameter('position', $position);
		$qb->setParameter('positionApprover', $positionApprover);
		
		return $qb->getQuery()->getSingleScalarResult();
	}
	
	public function getNumberClaimEachEmployeeForApproverHistory($position, $positionApprover, $from) {
		$expr = new Expr();
		$em   = $this->getContainer()->get('doctrine')->getManager();
		$qb   = $em->createQueryBuilder('approverHistory');
		$qb->select($qb->expr()->count('approverHistory.id'));
		$qb->from('AppBundle:approverHistory', 'approverHistory');
		$qb->where('approverHistory.position = :position');
		$qb->andWhere('approverHistory.approverPosition = :approverPosition');
		$qb->setParameter('position', $position);
		$qb->setParameter('approverPosition', $positionApprover);
		if($from != 'all') {
			$dateFilter = new  \DateTime($from);
			$qb->andWhere($expr->eq('approverHistory.periodFrom', ':periodFrom'));
			$qb->setParameter('periodFrom', $dateFilter->format('Y-m-d'));
		}
		
		return $qb->getQuery()->getSingleScalarResult();
	}
	
	public function isShowMenuForApprover($position) {
		$expr = new Expr();
		$em   = $this->getContainer()->get('doctrine')->getManager();
		$qb   = $em->createQueryBuilder('claim');
		$qb->select($qb->expr()->count('claim.id'));
		$qb->from('AppBundle:Claim', 'claim');
		$qb->where($expr->orX('claim.approverEmployee = :position', 'claim.approverBackupEmployee = :position'));
		$qb->andWhere($expr->eq('claim.status', ':statusCheckerApproved'));
		$qb->setParameter('statusCheckerApproved', Claim::STATUS_CHECKER_APPROVED);
		$qb->setParameter('position', $position);
		
		return $qb->getQuery()->getSingleScalarResult();
	}
	
	public function isShowMenuForApproverHistory($position) {
		$expr = new Expr();
		$em   = $this->getContainer()->get('doctrine')->getManager();
		$qb   = $em->createQueryBuilder('approverHistory');
		$qb->select($qb->expr()->count('approverHistory.id'));
		$qb->from('AppBundle:approverHistory', 'approverHistory');
		$qb->where($expr->eq('approverHistory.approverPosition', ':approverPosition'));
		$qb->setParameter('approverPosition', $position);
		
		return $qb->getQuery()->getSingleScalarResult();
	}
	
	
}
