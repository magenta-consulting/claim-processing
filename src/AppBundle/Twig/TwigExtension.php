<?php

namespace AppBundle\Twig;


use AppBundle\Entity\Claim;
use AppBundle\Entity\Position;
use Application\Sonata\MediaBundle\Entity\Media;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TwigExtension extends \Twig_Extension {
	/**
	 * @var ContainerInterface
	 */
	private $container;
	
	public function __construct($container) {
		$this->container = $container;
	}
	
	public function getParameter($name) {
		return $this->container->getParameter($name);
	}
	
	public function getNumberDecimalDigits($value) {
		$value = explode('.', $value);
		
		return count($value[1]);
		
	}
	
	public function getNumberClaimEachEmployeeForChecker($position, $positionChecker) {
		return $this->container->get('app.checker_rule')->getNumberClaimEachEmployeeForChecker($position, $positionChecker);
	}
	
	public function getNumberClaimEachEmployeeForCheckerHistory($position, $positionChecker, $from) {
		return $this->container->get('app.checker_rule')->getNumberClaimEachEmployeeForCheckerHistory($position, $positionChecker, $from);
	}
	
	public function getNumberClaimEachEmployeeForApprover($position, $positionApprover) {
		return $this->container->get('app.approver_rule')->getNumberClaimEachEmployeeForApprover($position, $positionApprover);
	}
	
	public function getNumberClaimEachEmployeeForApproverHistory($position, $positionApprover, $from) {
		return $this->container->get('app.approver_rule')->getNumberClaimEachEmployeeForApproverHistory($position, $positionApprover, $from);
	}
	
	public function getTotalAmountClaimEachEmployeeForHr($position, $from) {
		return $this->container->get('app.hr_rule')->getTotalAmountClaimEachEmployeeForHr($position, $from);
	}
	
	public function getTotalAmountClaimEachEmployeeForHrReport($position, $from) {
		return $this->container->get('app.hr_rule')->getTotalAmountClaimEachEmployeeForHrReport($position, $from);
	}
	
	
	public function isShowMenuForChecker(Position $position) {
		return $this->container->get('app.checker_rule')->isShowMenuForChecker($position);
	}
	
	public function isShowMenuForCheckerHistory($position) {
		return $this->container->get('app.checker_rule')->isShowMenuForCheckerHistory($position);
	}
	
	public function isShowMenuForApprover($position) {
		return $this->container->get('app.approver_rule')->isShowMenuForApprover($position);
	}
	
	public function isShowMenuForApproverHistory($position) {
		return $this->container->get('app.approver_rule')->isShowMenuForApproverHistory($position);
	}
	
	public function getUrlMedia($media, $context = 'default', $format = 'reference') {
		return $this->container->get('app.media.retriever')->getPublicURL($media, $context, $format);
	}
	
	public function getCurrentClaimPeriod($key) {
		return $this->container->get('app.claim_rule')->getCurrentClaimPeriod($key);
	}
	
	public function getFlexiPeriod($key) {
		return $this->container->get('app.claim_rule')->getFlexiPeriod($key);
	}
	
	public function isExceedLimitRule($claim, $position) {
		return $this->container->get('app.claim_rule')->isExceedLimitRule($claim, $position);
	}
	
	public function getLimitAmount($claim, $position) {
		return $this->container->get('app.claim_rule')->getLimitAmount($claim, $position);
	}
	
	public function getChecker($position) {
		return $this->container->get('app.checker_rule')->getChecker($position);
	}
	
	public function getApprover($position) {
		return $this->container->get('app.approver_rule')->getApprover($position);
	}
	
	public function getInforUserClaim($positionId) {
		if($positionId) {
			$position = $this->container->get('doctrine')->getManager()->getRepository('AppBundle\Entity\Position')->find($positionId);
		} else {
			$position = $this->container->get('app.claim_rule')->getPosition();
		}
		
		return $position;
	}
	
	public function isShowEditButtonForClaim(Claim $claim) {
		$listStatusAllow = [
			Claim::STATUS_DRAFT,
			Claim::STATUS_CHECKER_REJECTED,
			Claim::STATUS_APPROVER_REJECTED,
			Claim::STATUS_HR_REJECTED
		];
		if(in_array($claim->getStatus(), $listStatusAllow)) {
			return true;
		}
		
		return false;
	}
	
	public function isShowDeleteButtonForClaim(Claim $claim) {
		if($claim->getStatus() != Claim::STATUS_PROCESSED) {
			return true;
		}
		
		return false;
	}
	
	public function isShowApproveRejectCheckerButtonForClaim(Claim $claim) {
		$listStatusAllow = [
			Claim::STATUS_PENDING,
		];
		if(in_array($claim->getStatus(), $listStatusAllow)) {
			return true;
		}
		
		return false;
	}
	
	public function isShowApproveRejectApproverButtonForClaim(Claim $claim) {
		$listStatusAllow = [
			Claim::STATUS_CHECKER_APPROVED,
		];
		if(in_array($claim->getStatus(), $listStatusAllow)) {
			return true;
		}
		
		return false;
	}
	
	public function isShowDeleteRejectHrButtonForClaim(Claim $claim) {
		$listStatusAllow = [
			Claim::STATUS_APPROVER_APPROVED,
		];
		if(in_array($claim->getStatus(), $listStatusAllow)) {
			return true;
		}
		
		return false;
	}
	
	public function getNumberRejectedClaim() {
		$number = $this->container->get('app.claim_rule')->getNumberRejectedClaim();
		if($number > 0) {
			return $number;
		}
		
		return '';
	}
	
	public function getCheckerNotification() {
		return $this->container->get('app.claim_notification')->getCheckerMessageNotification();
	}
	
	public function getApproverNotification() {
		return $this->container->get('app.claim_notification')->getApproverMessageNotification();
	}
	
	public function getProcessedDate($from, $position) {
		return $this->container->get('app.hr_rule')->getProcessedDate($from, $position);
	}
	
	public function getNameUser($id) {
		return $this->container->get('app.claim_rule')->getNameUser($id);
	}
	
	public function isHaveFlexiClaim() {
		return $this->container->get('app.claim_rule')->isHaveFlexiClaim();
	}
	
	
	public function getDescriptionEmployeeGroup($employeeGroup) {
		return $this->container->get('app.claim_rule')->getDescriptionEmployeeGroup($employeeGroup);
	}
	
	public function getCurrencyDefault() {
		$currency = $this->container->get('app.claim_rule')->getCurrencyDefault();
		if($currency) {
			return $currency->getCode();
		}
		
		return '';
	}
	
	public function getFunctions() {
		return array(
			'getParameter'                                 => new \Twig_Function_Method($this, 'getParameter', array( 'is_safe' => array( 'html' ) )),
			'getNumberDecimalDigits'                       => new \Twig_Function_Method($this, 'getNumberDecimalDigits', array( 'is_safe' => array( 'html' ) )),
			'getUrlMedia'                                  => new \Twig_Function_Method($this, 'getUrlMedia', array( 'is_safe' => array( 'html' ) )),
			'getNumberClaimEachEmployeeForChecker'         => new \Twig_Function_Method($this, 'getNumberClaimEachEmployeeForChecker', array( 'is_safe' => array( 'html' ) )),
			'getNumberClaimEachEmployeeForApprover'        => new \Twig_Function_Method($this, 'getNumberClaimEachEmployeeForApprover', array( 'is_safe' => array( 'html' ) )),
			'isShowMenuForChecker'                         => new \Twig_Function_Method($this, 'isShowMenuForChecker', array( 'is_safe' => array( 'html' ) )),
			'isShowMenuForApprover'                        => new \Twig_Function_Method($this, 'isShowMenuForApprover', array( 'is_safe' => array( 'html' ) )),
			'getCurrentClaimPeriod'                        => new \Twig_Function_Method($this, 'getCurrentClaimPeriod', array( 'is_safe' => array( 'html' ) )),
			'isExceedLimitRule'                            => new \Twig_Function_Method($this, 'isExceedLimitRule', array( 'is_safe' => array( 'html' ) )),
			'getLimitAmount'                               => new \Twig_Function_Method($this, 'getLimitAmount', array( 'is_safe' => array( 'html' ) )),
			'isShowEditButtonForClaim'                     => new \Twig_Function_Method($this, 'isShowEditButtonForClaim', array( 'is_safe' => array( 'html' ) )),
			'isShowApproveRejectCheckerButtonForClaim'     => new \Twig_Function_Method($this, 'isShowApproveRejectCheckerButtonForClaim', array( 'is_safe' => array( 'html' ) )),
			'isShowApproveRejectApproverButtonForClaim'    => new \Twig_Function_Method($this, 'isShowApproveRejectApproverButtonForClaim', array( 'is_safe' => array( 'html' ) )),
			'getChecker'                                   => new \Twig_Function_Method($this, 'getChecker', array( 'is_safe' => array( 'html' ) )),
			'getApprover'                                  => new \Twig_Function_Method($this, 'getApprover', array( 'is_safe' => array( 'html' ) )),
			'getNumberRejectedClaim'                       => new \Twig_Function_Method($this, 'getNumberRejectedClaim', array( 'is_safe' => array( 'html' ) )),
			'getInforUserClaim'                            => new \Twig_Function_Method($this, 'getInforUserClaim', array( 'is_safe' => array( 'html' ) )),
			'getCheckerNotification'                       => new \Twig_Function_Method($this, 'getCheckerNotification', array( 'is_safe' => array( 'html' ) )),
			'getApproverNotification'                      => new \Twig_Function_Method($this, 'getApproverNotification', array( 'is_safe' => array( 'html' ) )),
			'isShowDeleteButtonForClaim'                   => new \Twig_Function_Method($this, 'isShowDeleteButtonForClaim', array( 'is_safe' => array( 'html' ) )),
			'getDescriptionEmployeeGroup'                  => new \Twig_Function_Method($this, 'getDescriptionEmployeeGroup', array( 'is_safe' => array( 'html' ) )),
			'getCurrencyDefault'                           => new \Twig_Function_Method($this, 'getCurrencyDefault', array( 'is_safe' => array( 'html' ) )),
			'getTotalAmountClaimEachEmployeeForHr'         => new \Twig_Function_Method($this, 'getTotalAmountClaimEachEmployeeForHr', array( 'is_safe' => array( 'html' ) )),
			'isShowDeleteRejectHrButtonForClaim'           => new \Twig_Function_Method($this, 'isShowDeleteRejectHrButtonForClaim', array( 'is_safe' => array( 'html' ) )),
			'getTotalAmountClaimEachEmployeeForHrReport'   => new \Twig_Function_Method($this, 'getTotalAmountClaimEachEmployeeForHrReport', array( 'is_safe' => array( 'html' ) )),
			'getProcessedDate'                             => new \Twig_Function_Method($this, 'getProcessedDate', array( 'is_safe' => array( 'html' ) )),
			'getNumberClaimEachEmployeeForCheckerHistory'  => new \Twig_Function_Method($this, 'getNumberClaimEachEmployeeForCheckerHistory', array( 'is_safe' => array( 'html' ) )),
			'getNumberClaimEachEmployeeForApproverHistory' => new \Twig_Function_Method($this, 'getNumberClaimEachEmployeeForApproverHistory', array( 'is_safe' => array( 'html' ) )),
			'isShowMenuForApproverHistory'                 => new \Twig_Function_Method($this, 'isShowMenuForApproverHistory', array( 'is_safe' => array( 'html' ) )),
			'isShowMenuForCheckerHistory'                  => new \Twig_Function_Method($this, 'isShowMenuForCheckerHistory', array( 'is_safe' => array( 'html' ) )),
			'getNameUser'                                  => new \Twig_Function_Method($this, 'getNameUser', array( 'is_safe' => array( 'html' ) )),
			'getFlexiPeriod'                               => new \Twig_Function_Method($this, 'getFlexiPeriod', array( 'is_safe' => array( 'html' ) )),
			'isHaveFlexiClaim'                             => new \Twig_Function_Method($this, 'isHaveFlexiClaim', array( 'is_safe' => array( 'html' ) )),
		
		);
	}
	
	public function getName() {
		return 'app_extension';
	}
	
}
