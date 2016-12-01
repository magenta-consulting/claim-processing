<?php

namespace AppBundle\Services;

use AppBundle\Entity\Claim;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Context\ExecutionContext;
use AppBundle\Services\ClaimRule;

class ApproverRule extends ClaimRule
{

    public function getApprover($position)
    {
        $expr = new Expr();
        $em = $this->container->get('doctrine')->getManager();
        //may be will have many approver, but the priority for more detail group
        $employeeGroupBelongToUser = $this->getEmployeeGroupBelongToUser($position);
        for ($i = count($employeeGroupBelongToUser) - 1; $i >= 0; $i--) {
            $approver = $em->createQueryBuilder()
                ->select('approvalAmountPolicies')
                ->from('AppBundle\Entity\ApprovalAmountPolicies', 'approvalAmountPolicies')
                ->join('approvalAmountPolicies.approvalAmountPoliciesEmployeeGroups', 'approvalAmountPoliciesEmployeeGroup')
                ->join('approvalAmountPoliciesEmployeeGroup.employeeGroup', 'employeeGroup')
                ->where($expr->eq('employeeGroup.description', ':employeeGroup'))
                ->setParameter('employeeGroup', $employeeGroupBelongToUser[$i])
                ->getQuery()->getOneOrNullResult();
            if ($approver) {
                return $approver;
            }
        }
        return null;

    }

    public function assignClaimToSpecificApprover(Claim $claim, $position)
    {
        $approver = $this->getApprover($position);
        $amount = $claim->getClaimAmount();
        if ($approver) {
            //check approver1 can approve ?
            if ($approver->getApprover1() && $approver->isApproval1AmountStatus()) {
                if ($approver->getApproval1Amount()) {
                    if ($approver->getApproval1Amount() >= $amount) {
                        if ($approver->getApprover1()->getId() != $this->getPosition()->getId()) {
                            $result['approverEmployee'] = $approver->getApprover1();
                        } else {
                            $result['approverEmployee'] = $approver->getOverrideApprover1();
                        }
                        $result['approverBackupEmployee'] = $approver->getBackupApprover1();
                        return $result;
                    }
                } else {
                    if ($approver->getApprover1()->getId() != $this->getPosition()->getId()) {
                        $result['approverEmployee'] = $approver->getApprover1();
                    } else {
                        $result['approverEmployee'] = $approver->getOverrideApprover1();
                    }
                    $result['approverBackupEmployee'] = $approver->getBackupApprover1();
                    return $result;
                }
            }
            //check approver2 can approve ?
            if ($approver->getApprover2() && $approver->isApproval2AmountStatus()) {
                if ($approver->getApproval2Amount()) {
                    if ($approver->getApproval2Amount() >= $amount) {
                        if ($approver->getApprover2()->getId() != $this->getPosition()->getId()) {
                            $result['approverEmployee'] = $approver->getApprover2();
                        } else {
                            $result['approverEmployee'] = $approver->getOverrideApprover2();
                        }
                        $result['approverBackupEmployee'] = $approver->getBackupApprover2();
                        return $result;
                    }
                } else {
                    if ($approver->getApprover2()->getId() != $this->getPosition()->getId()) {
                        $result['approverEmployee'] = $approver->getApprover2();
                    } else {
                        $result['approverEmployee'] = $approver->getOverrideApprover2();
                    }
                    $result['approverBackupEmployee'] = $approver->getBackupApprover2();
                    return $result;
                }
            }
            //check approver3 can approve ?
            if ($approver->getApprover3() && $approver->isApproval3AmountStatus()) {
                if ($approver->getApproval3Amount()) {
                    if ($approver->getApproval3Amount() >= $amount) {
                        if ($approver->getApprover3()->getId() != $this->getPosition()->getId()) {
                            $result['approverEmployee'] = $approver->getApprover3();
                        } else {
                            $result['approverEmployee'] = $approver->getOverrideApprover3();
                        }
                        $result['approverBackupEmployee'] = $approver->getBackupApprover3();
                        return $result;
                    }
                } else {
                    if ($approver->getApprover3()->getId() != $this->getPosition()->getId()) {
                        $result['approverEmployee'] = $approver->getApprover3();
                    } else {
                        $result['approverEmployee'] = $approver->getOverrideApprover3();
                    }
                    $result['approverBackupEmployee'] = $approver->getBackupApprover3();
                    return $result;
                }
            }
        }
        return ['approverEmployee' => null, 'approverBackupEmployee' => null];
    }

    public function getListClaimPeriodForFilterApprover()
    {
        $expr = new Expr();
        $position = $this->getPosition();
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select('claim');
        $qb->from('AppBundle:Claim', 'claim');
        $qb->where($expr->orX('claim.approverEmployee = :position', 'claim.approverBackupEmployee = :position'));
        $qb->andWhere($expr->eq('claim.status', ':statusCheckerApproved'));
        $qb->orderBy('claim.createdAt', 'DESC');
        $qb->setParameter('statusCheckerApproved', Claim::STATUS_CHECKER_APPROVED);
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
    public function getListClaimPeriodForFilterApproverHistory()
    {
        $expr = new Expr();
        $position = $this->getPosition();
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('approverHistory');
        $qb->select('approverHistory');
        $qb->from('AppBundle:approverHistory', 'approverHistory');
        $qb->join('approverHistory.claim', 'claim');
        $qb->andWhere($expr->eq('approverHistory.approverPosition', ':approverPosition'));
        $qb->orderBy('claim.createdAt', 'DESC');
        $qb->setParameter('approverPosition', $position);
        $approverHistories = $qb->getQuery()->getResult();

        $listPeriod = ['Show All'=>'all'];
        $from = $this->getCurrentClaimPeriod('from');
        $to = $this->getCurrentClaimPeriod('to');
        $listPeriod[$from->format('d M Y') . ' - ' . $to->format('d M Y')] = $from->format('Y-m-d');
        foreach ($approverHistories as $approverHistory) {
            $listPeriod[$approverHistory->getPeriodFrom()->format('d M Y') . ' - ' . $approverHistory->getPeriodTo()->format('d M Y')] = $approverHistory->getPeriodFrom()->format('Y-m-d');
        }
        return $listPeriod;
    }

    public function getNumberClaimEachEmployeeForApprover($position, $positionApprover)
    {
        $expr = new Expr();
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
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

    public function getNumberClaimEachEmployeeForApproverHistory($position, $positionApprover,$from)
    {
        $expr = new Expr();
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('approverHistory');
        $qb->select($qb->expr()->count('approverHistory.id'));
        $qb->from('AppBundle:approverHistory', 'approverHistory');
        $qb->where('approverHistory.position = :position');
        $qb->andWhere('approverHistory.approverPosition = :approverPosition');
        $qb->setParameter('position', $position);
        $qb->setParameter('approverPosition', $positionApprover);
        if ($from !='all') {
            $dateFilter = new  \DateTime($from);
            $qb->andWhere($expr->eq('approverHistory.periodFrom', ':periodFrom'));
            $qb->setParameter('periodFrom', $dateFilter->format('Y-m-d'));
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function isShowMenuForApprover($position)
    {
        $expr = new Expr();
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select($qb->expr()->count('claim.id'));
        $qb->from('AppBundle:Claim', 'claim');
        $qb->where($expr->orX('claim.approverEmployee = :position', 'claim.approverBackupEmployee = :position'));
        $qb->andWhere($expr->eq('claim.status', ':statusCheckerApproved'));
        $qb->setParameter('statusCheckerApproved', Claim::STATUS_CHECKER_APPROVED);
        $qb->setParameter('position', $position);

        return $qb->getQuery()->getSingleScalarResult();
    }


}
