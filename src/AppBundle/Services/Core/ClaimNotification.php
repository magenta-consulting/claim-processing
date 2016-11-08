<?php

namespace AppBundle\Services\Core;

use AppBundle\Entity\Claim;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Context\ExecutionContext;

class ClaimNotification
{
    use ContainerAwareTrait;


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
        return  $query->getQuery()->getResult();
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

        return  $query->getQuery()->getResult();
    }
}
