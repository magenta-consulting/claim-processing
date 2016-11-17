<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Consumer;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use AppBundle\Entity\Claim;
use Doctrine\ORM\Query\Expr;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Context\ExecutionContext;
use Sonata\NotificationBundle\Model\MessageInterface;

class ClaimMailerConsumer implements ConsumerInterface
{
    /**
     * @var
     */
    protected $container;
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @param \Swift_Mailer $mailer
     */
    public function __construct(ContainerInterface $containner, \Swift_Mailer $mailer)
    {
        $this->container = $containner;
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ConsumerEvent $event)
    {
        echo 'sending';
        $this->container->get('app.claim_notification')->sendNotification();
    }


}
