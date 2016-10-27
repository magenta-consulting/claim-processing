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
        $value = explode('.', $value);
        return count($value[1]);

    }

    public function getNumberClaim($position, $positionChecker)
    {
        return $this->container->get('app.claim_rule')->getNumberClaim($position, $positionChecker);
    }

    public function isShowMenuForChecker($position)
    {
        return $this->container->get('app.claim_rule')->isShowMenuForChecker($position);
    }

    public function getUrlMedia($media, $context = 'default', $format = 'reference')
    {
        return $this->container->get('app.media.retriever')->getPublicURL($media, $context, $format);
    }

    public function getCurrentClaimPeriod($key)
    {
        return $this->container->get('app.claim_rule')->getCurrentClaimPeriod($key);
    }

    public function isExceedLimitRule($claim)
    {
        return $this->container->get('app.claim_rule')->isExceedLimitRule($claim);
    }

    public function getLimitAmount($claim)
    {
        return $this->container->get('app.claim_rule')->getLimitAmount($claim);
    }

    public function getFunctions()
    {
        return array(
            'getParameter' => new \Twig_Function_Method($this, 'getParameter', array('is_safe' => array('html'))),
            'getNumberDecimalDigits' => new \Twig_Function_Method($this, 'getNumberDecimalDigits', array('is_safe' => array('html'))),
            'getUrlMedia' => new \Twig_Function_Method($this, 'getUrlMedia', array('is_safe' => array('html'))),
            'getNumberClaim' => new \Twig_Function_Method($this, 'getNumberClaim', array('is_safe' => array('html'))),
            'isShowMenuForChecker' => new \Twig_Function_Method($this, 'isShowMenuForChecker', array('is_safe' => array('html'))),
            'getCurrentClaimPeriod' => new \Twig_Function_Method($this, 'getCurrentClaimPeriod', array('is_safe' => array('html'))),
            'isExceedLimitRule' => new \Twig_Function_Method($this, 'isExceedLimitRule', array('is_safe' => array('html'))),
            'getLimitAmount' => new \Twig_Function_Method($this, 'getLimitAmount', array('is_safe' => array('html'))),
        );
    }

    public function getName()
    {
        return 'app_extension';
    }

}
