<?php

namespace AppBundle\Twig;


use AppBundle\Entity\Booking\Booking;
use AppBundle\Entity\Space\Location;
use AppBundle\Entity\Space\Space;
use Application\Sonata\MediaBundle\Entity\Media;

class TwigExtension extends \Twig_Extension
{

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function getParameter($name)
    {
        return $this->container->getParameter($name);
    }
   public function getNumberDecimalDigits($value)
    {
        $value = explode('.',$value);
        return count($value[1]);

    }
    public function getNumberClaim($position,$checker){
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select($qb->expr()->count('claim.id'));
        $qb->from('AppBundle:Claim','claim');
        $qb->where('claim.position = :position');
        $qb->andWhere('claim.checker = :checker');
        $qb->setParameter('position', $position);
        $qb->setParameter('checker', $checker);

        return $qb->getQuery()->getSingleScalarResult();
    }
    public function isShowMenuForChecker($position){
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder('claim');
        $qb->select($qb->expr()->count('claim.id'));
        $qb->from('AppBundle:Claim','claim');
        $qb->join('claim.checker','checker');
        $qb->where('checker.checker = :position');
        $qb->setParameter('position', $position);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getUrlMedia($media){
        return $this->container->get('app.media.retriever')->getPublicURL($media);
    }
    public function getRuleForClaim($claim){
        return $this->container->get('app.claim_rule')->getRuleForClaim($claim);
    }


    public function getFunctions()
    {
        return array(
            'getParameter' => new \Twig_Function_Method($this, 'getParameter', array('is_safe' => array('html'))),
            'getNumberDecimalDigits' => new \Twig_Function_Method($this, 'getNumberDecimalDigits', array('is_safe' => array('html'))),
            'getUrlMedia' => new \Twig_Function_Method($this, 'getUrlMedia', array('is_safe' => array('html'))),
            'getNumberClaim' => new \Twig_Function_Method($this, 'getNumberClaim', array('is_safe' => array('html'))),
            'isShowMenuForChecker' => new \Twig_Function_Method($this, 'isShowMenuForChecker', array('is_safe' => array('html'))),
            'getRuleForClaim' => new \Twig_Function_Method($this, 'getRuleForClaim', array('is_safe' => array('html'))),
        );
    }

    public function getName()
    {
        return 'app_extension';
    }

}
