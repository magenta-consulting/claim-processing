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
            $expr->orX(
                $expr->eq('checker.checker', ':checker'),
                $expr->eq('checker.backupChecker', ':checker')
            )
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
            $expr->orX(
                $expr->eq('claim.approverEmployee', ':position'),
                $expr->eq('claim.approverBackupEmployee', ':position')
            )
        );
        $query->andWhere($expr->eq('claim.status', ':statusCheckerApproved'));
        $query->setParameter('statusCheckerApproved', Claim::STATUS_CHECKER_APPROVED);
        $query->setParameter('position', $approver);
        return $query->getQuery()->getResult();
    }

    public function sendmailToChecker($checker, $listPosition)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Notification For Checker')
            ->setFrom('noreply@magentapulse.com')
            ->setTo($checker->getEmail())
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
            ->setSubject('Notification For Approver')
            ->setFrom('noreply@magentapulse.com')
            ->setTo($approver->getEmail())
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
