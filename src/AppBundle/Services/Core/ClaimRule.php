<?php

namespace AppBundle\Services\Core;

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
        if ($company->getParent()) {
            return $company->getParent();
        }
        return $company;
    }

    public function calculateTaxAmount($receiptAmount, $taxRate)
    {
        $taxAmount = $receiptAmount + $taxRate;
        return $taxAmount;
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


    /*2 for checker--------------------------------------*/
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


    /*3 for approver--------------------------------------*/
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

        $listPeriod = [];
        foreach ($claims as $claim) {
            $listPeriod[$claim->getPeriodFrom()->format('d M Y') . ' - ' . $claim->getPeriodTo()->format('d M Y')] = $claim->getPeriodFrom()->format('Y-m-d');
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
            $expr->eq('claim.status', ':statusApproverRejected')
        ));
        $query->setParameter('periodFrom', $periodFrom->format('Y-m-d'));
        $query->setParameter('periodTo', $periodTo->format('Y-m-d'));
        $query->setParameter('statusCheckerRejected', Claim::STATUS_CHECKER_REJECTED);
        $query->setParameter('statusApproverRejected', Claim::STATUS_APPROVER_REJECTED);
        $query->setParameter('position', $this->getPosition());
        return $query->getQuery()->getSingleScalarResult();
    }

    /** employee */
    public function getListClaimPeriodForFilterEmployee()
    {
        $expr = new Expr();
        $position = $this->getPosition();
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select('claim');
        $qb->from('AppBundle:Claim', 'claim');
        $qb->orderBy('claim.createdAt', 'DESC');
        $qb->where('claim.position = :position');
        $qb->setParameter('position', $position);
        $claims = $qb->getQuery()->getResult();

        $listPeriod = [];
        foreach ($claims as $claim) {
            $listPeriod[$claim->getPeriodFrom()->format('d M Y') . ' - ' . $claim->getPeriodTo()->format('d M Y')] = $claim->getPeriodFrom()->format('Y-m-d');
        }
        return $listPeriod;
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

    /*** notification ****--------------------*/

    public function getCheckerNotification()
    {

        $em = $this->container->get('doctrine')->getManager();
        $expr = new Expr();
        $clientCompany = $this->getClientCompany();
        $company = $this->getCompany();
        $position = $this->getPosition();
        $query = $em->createQueryBuilder('position');
        $query->select('position');
        $query->from('AppBundle:Position', 'position');
        $query->leftJoin('position.claims', 'claim');
        $query->leftJoin('claim.checker', 'checker');
        $query->leftJoin('position.company', 'company');
        $query->andWhere(
            $expr->orX(
                $expr->eq('company.parent', ':clientCompany'),
                $expr->eq('company', ':company')
            )
        );
        $query->andWhere(
            $expr->orX(
                $expr->eq('checker.checker', ':checker'),
                $expr->eq('checker.backupChecker', ':checker')
            )
        );
        $query->andWhere($expr->eq('claim.status', ':statusPending'));
        $query->setParameter('statusPending', Claim::STATUS_PENDING);
        $query->setParameter('checker', $position);
        $query->setParameter('company', $company);
        $query->setParameter('clientCompany', $clientCompany);
        $query->setMaxResults(20);
        $query->setFirstResult(0);
        return $query->getQuery()->getResult();
    }

    public function getApproverNotification()
    {
        $em = $this->container->get('doctrine')->getManager();
        $expr = new Expr();
        $clientCompany = $this->getClientCompany();
        $company = $this->getCompany();
        $position = $this->getPosition();
        $query = $em->createQueryBuilder('position');
        $query->select('position');
        $query->from('AppBundle:Position', 'position');
        $query->leftJoin('position.claims', 'claim');
        $query->leftJoin('position.company', 'company');
        $query->andWhere(
            $expr->orX(
                $expr->eq('company.parent', ':clientCompany'),
                $expr->eq('company', ':company')
            )
        );
        $query->andWhere(
            $expr->orX(
                $expr->eq('claim.approverEmployee', ':position'),
                $expr->eq('claim.approverBackupEmployee', ':position')
            )
        );
        $query->andWhere($expr->eq('claim.status', ':statusCheckerApproved'));
        $query->setParameter('statusCheckerApproved', Claim::STATUS_CHECKER_APPROVED);
        $query->setParameter('position', $position);
        $query->setParameter('company', $company);
        $query->setParameter('clientCompany', $clientCompany);
        $query->setMaxResults(20);
        $query->setFirstResult(0);

        return $query->getQuery()->getResult();
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
}
