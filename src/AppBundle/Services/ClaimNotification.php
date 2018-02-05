<?php

namespace AppBundle\Services;

use AppBundle\Entity\Claim;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Context\ExecutionContext;
use AppBundle\Services\ClaimRule;

class ClaimNotification extends ClaimRule
{

    /*** notification message****--------------------*/
    public function getCheckerMessageNotification()
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

    public function getApproverMessageNotification()
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
	
	    $query->andWhere($expr->in('claim.status', ':states'));
	    $query->setParameter('states', [
		    Claim::STATUS_CHECKER_APPROVED,
		    Claim::STATUS_APPROVER_APPROVED_FIRST,
		    Claim::STATUS_APPROVER_APPROVED_SECOND,
		    Claim::STATUS_APPROVER_APPROVED_THIRD
	    ]);
        
        $query->setParameter('position', $position);
        $query->setParameter('company', $company);
        $query->setParameter('clientCompany', $clientCompany);
        $query->setMaxResults(20);
        $query->setFirstResult(0);

        return $query->getQuery()->getResult();
    }
    /*** notification mail****--------------------*/

    public function getCheckerNotification($checker)
    {

        $em = $this->container->get('doctrine')->getManager();
        $expr = new Expr();
        $query = $em->createQueryBuilder('position');
        $query->select('position');
        $query->from('AppBundle:Position', 'position');
        $query->leftJoin('position.claims', 'claim');
        $query->leftJoin('claim.checker', 'checker');
        $query->andWhere(
                $expr->eq('checker.checker', ':checker')
        );
        $query->andWhere($expr->eq('claim.status', ':statusPending'));
        $query->setParameter('statusPending', Claim::STATUS_PENDING);
        $query->setParameter('checker', $checker);
        return $query->getQuery()->getResult();
    }

    public function getApproverNotification($approver)
    {
        $em = $this->container->get('doctrine')->getManager();
        $expr = new Expr();
        $query = $em->createQueryBuilder('position');
        $query->select('position');
        $query->from('AppBundle:Position', 'position');
        $query->leftJoin('position.claims', 'claim');
        $query->andWhere(
                $expr->eq('claim.approverEmployee', ':position')
        );
	
	    $query->andWhere($expr->in('claim.status', ':states'));
	    $query->setParameter('states', [
		    Claim::STATUS_CHECKER_APPROVED,
		    Claim::STATUS_APPROVER_APPROVED_FIRST,
		    Claim::STATUS_APPROVER_APPROVED_SECOND,
		    Claim::STATUS_APPROVER_APPROVED_THIRD
	    ]);
        
        $query->setParameter('position', $approver);
        return $query->getQuery()->getResult();
    }

    public function sendmailToChecker($checker, $listPosition)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('New Employee Claims Requiring Your Action')
            ->setFrom('noreply@magentapulse.com')
            ->setTo($checker->getEmail())
//            ->setTo('tuandumikedu@gmail.com')
            ->setBody(
                $this->container->get('twig')->render(
                    'AppBundle:SonataAdmin/Emails:notification_checker.html.twig',
                    array('resultCheckerNotification' => $listPosition,'checker'=>$checker)
                ),
                'text/html'
            );
        $this->container->get('mailer')->send($message);
        $spool = $this->container->get('mailer')->getTransport()->getSpool();
        $transport = $this->container->get('swiftmailer.transport.real');
        $spool->flushQueue($transport);
    }

    public function sendmailToApprover($approver, $listPosition)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('New Employee Claims Requiring Your Action')
            ->setFrom('noreply@magentapulse.com')
            ->setTo($approver->getEmail())
//            ->setTo('tuandumikedu@gmail.com')
            ->setBody(
                $this->container->get('twig')->render(
                    'AppBundle:SonataAdmin/Emails:notification_approver.html.twig',
                    array('resultApproverNotification' => $listPosition,'approver'=>$approver)
                ),
                'text/html'
            );
        $this->container->get('mailer')->send($message);
        $spool = $this->container->get('mailer')->getTransport()->getSpool();
        $transport = $this->container->get('swiftmailer.transport.real');
        $spool->flushQueue($transport);
    }

    public function sendNotification()
    {
        $em = $this->container->get('doctrine')->getManager();
        $checkersOrApprovers = $em->getRepository('AppBundle\Entity\Position')->findAll();
        foreach ($checkersOrApprovers as $checkerOrApprover) {
            $positionHasClaimRequireChecking = $this->getCheckerNotification($checkerOrApprover);
            if (count($positionHasClaimRequireChecking)) {
                $this->sendmailToChecker($checkerOrApprover, $positionHasClaimRequireChecking);
            }
            $positionHasClaimRequireApproving = $this->getApproverNotification($checkerOrApprover);
            if (count($positionHasClaimRequireApproving)) {
                $this->sendmailToApprover($checkerOrApprover, $positionHasClaimRequireApproving);
            }
        }
    }


}
