<?php
/**
 * Created by PhpStorm.
 * User: chuyennguyen
 * Date: 8/12/16
 * Time: 6:23 PM
 */

namespace AppBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Gallery;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DoctrineExtensions\Query\Mysql\Date;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="claim")
 */
class Claim {
	const STATUS_NOT_USE = 'NOT_USE';
	const STATUS_DRAFT = 'DRAFT';
	const STATUS_PENDING = 'PENDING';
	const STATUS_CHECKER_APPROVED = 'CHECKER_APPROVED';
	const STATUS_CHECKER_REJECTED = 'CHECKER_REJECTED';
	const STATUS_APPROVER_APPROVED = 'APPROVER_APPROVED';
	const STATUS_APPROVER_APPROVED_FIRST = 'APPROVER_APPROVED_FIRST';
	const STATUS_APPROVER_APPROVED_SECOND = 'APPROVER_APPROVED_SECOND';
	const STATUS_APPROVER_APPROVED_THIRD = 'APPROVER_APPROVED_THIRD';
	const STATUS_APPROVER_REJECTED = 'APPROVER_REJECTED';
	const STATUS_HR_REJECTED = 'HR_REJECTED';
	const STATUS_PROCESSED = 'PROCESSED';
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer",options={"unsigned":true})
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	/**
	 * @var Company
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
	 * @ORM\JoinColumn(name="company_id", referencedColumnName="id",onDelete="CASCADE")
	 */
	private $company;
	
	
	/**
	 * @var ClaimType
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClaimType")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 */
	private $claimType;
	/**
	 * @var CurrencyExchange
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CurrencyExchange")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 */
	private $currencyExchange;
	/**
	 * @var ClaimCategory
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClaimCategory")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 */
	private $claimCategory;
	
	/**
	 * @var float
	 * @ORM\Column(name="claim_amount",type="float",nullable=true)
	 */
	private $claimAmount;
	
	/**
	 * @var float
	 * @ORM\Column(name="claim_amount_converted",type="float",nullable=true)
	 */
	private $claimAmountConverted;
	
	/**
	 * @var text
	 * @ORM\Column(name="description",type="text",nullable=true)
	 */
	private $description;
	
	
	/**
	 * @var date
	 * @ORM\Column(name="receipt_date",type="date",nullable=true)
	 */
	private $receiptDate;
	/**
	 * @var date
	 * @ORM\Column(name="processed_date",type="datetime",nullable=true)
	 */
	private $processedDate;
	
	/**
	 * @var string
	 * @ORM\Column(name="submission_remarks",type="text",nullable=true)
	 */
	private $submissionRemarks;
	
	/**
	 * @var User
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position",inversedBy="claims")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 */
	private $position;
	
	/**
	 * @var ClaimMedia
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\ClaimMedia",mappedBy="claim",cascade={"persist","remove"},orphanRemoval=true)
	 */
	private $claimMedias;
	
	/**
	 * @var Checker
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Checker",cascade={"persist"},inversedBy="claims")
	 * @ORM\JoinColumn(onDelete="SET NULL")
	 */
	private $checker;
	
	/**
	 * @var ApprovalAmountPolicies
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ApprovalAmountPolicies",cascade={"persist"},inversedBy="claims")
	 * @ORM\JoinColumn(onDelete="SET NULL")
	 */
	private $approver;
	
	/**
	 * @var Position
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
	 * @ORM\JoinColumn(onDelete="SET NULL")
	 */
	private $approverEmployee;
	/**
	 * @var Position
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
	 * @ORM\JoinColumn(onDelete="SET NULL")
	 */
	private $approverBackupEmployee;
	
	/**
	 * @var TaxRate
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TaxRate")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 */
	private $taxRate;
	
	/**
	 * @var float
	 * @ORM\Column(name="tax_amount",type="float",nullable=true)
	 */
	private $taxAmount;
	
	/**
	 * @var float
	 * @ORM\Column(name="tax_amount_converted",type="float",nullable=true)
	 */
	private $taxAmountConverted;
	
	/**
	 * @var \DateTime
	 * @ORM\Column(name="period_from",type="date",nullable=true)
	 */
	private $periodFrom;
	/**
	 * @var \DateTime
	 * @ORM\Column(name="period_to",type="date",nullable=true)
	 */
	private $periodTo;
	
	
	/**
	 * @var \DateTime
	 * @ORM\Column(name="created_at",type="datetime")
	 */
	private $createdAt;
	
	/**
	 * @var Position
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 */
	private $createdBy;
	
	/**
	 * @var string
	 * @ORM\Column(name="status",type="string")
	 */
	private $status;
	/**
	 * @var string
	 * @ORM\Column(name="checker_remark",type="string",nullable=true)
	 */
	private $checkerRemark;
	
	/**
	 * @var string
	 * @ORM\Column(name="approver_remark",type="string",nullable=true)
	 */
	private $approverRemark;
	
	/**
	 * @var \DateTime
	 * @ORM\Column(name="checker_updated_at",type="datetime",nullable=true)
	 */
	private $checkerUpdatedAt;
	/**
	 * @var \DateTime
	 * @ORM\Column(name="approver_updated_at",type="datetime",nullable=true)
	 */
	private $approverUpdatedAt;
	
	/**
	 * @var float
	 * @ORM\Column(name="ex_rate",type="string",nullable=true)
	 */
	private $exRate;
	/**
	 * @var PayCode
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PayCode")
	 * @ORM\JoinColumn(onDelete="CASCADE")
	 */
	private $payCode;
	
	/**
	 * @var CheckerHistory
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\CheckerHistory",mappedBy="claim")
	 */
	private $checkingHistories;
	
	/**
	 * @var CheckerHistory
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\ApproverHistory",mappedBy="claim")
	 */
	private $approverHistories;
	
	/**
	 * @var boolean
	 * @ORM\Column(name="flexi_claim",type="boolean",options={"default":0})
	 */
	private $flexiClaim;
	
	
	public function __construct() {
		$this->createdAt         = new \DateTime();
		$this->claimMedias       = new ArrayCollection();
		$this->checkingHistories = new ArrayCollection();
		$this->approverHistories = new ArrayCollection();
		$this->status            = self::STATUS_NOT_USE;
		$this->flexiClaim        = false;
	}
	
	public $lastApprover = null;
	public $lastBackupApprover = null;
	
	/**
	 * @param string $status
	 */
	public function setStatus($status) {
		if($this->status !== self::STATUS_APPROVER_APPROVED && $status === self::STATUS_APPROVER_APPROVED) {
			switch($this->approverEmployee->getId()) {
				case $this->approver->getApprover1()->getId():
				case $this->approver->getOverrideApprover1()->getId():
					$status = self::STATUS_APPROVER_APPROVED_FIRST;
					break;
				case $this->approver->getApprover2()->getId():
				case $this->approver->getOverrideApprover2()->getId():
					$status = self::STATUS_APPROVER_APPROVED_SECOND;
					break;
			}
			
			$this->lastApprover       = $this->getApproverEmployee();
			$this->lastBackupApprover = $this->getApproverBackupEmployee();
		}
		
		$this->status = $status;
		$result       = $this->getApproverToAssign();
		
		$this->setApproverEmployee($result['approverEmployee']);
		$this->setApproverBackupEmployee($result['approverBackupEmployee']);
		
	}
	
	public function getApproverAfterNextToAssign(ApprovalAmountPolicies $approvalAmountPolicy = null) {
		$bkStatus = $this->status;
		switch($bkStatus) {
			case self::STATUS_CHECKER_APPROVED:
				$this->status = self::STATUS_APPROVER_APPROVED_SECOND;
				break;
			case self::STATUS_APPROVER_APPROVED_FIRST:
				$this->status = self::STATUS_APPROVER_APPROVED_THIRD;
				break;
			case self::STATUS_APPROVER_APPROVED_SECOND:
				return null;
		}
		$result       = $this->getApproverToAssign($approvalAmountPolicy);
		$this->status = $bkStatus;
		if(empty($result['approverEmployee']) && empty($result['approverBackupEmployee'])) {
			return null;
		}
		
		return $result;
	}
	
	public function getNextApproverToAssign(ApprovalAmountPolicies $approvalAmountPolicy = null) {
		$bkStatus = $this->status;
		switch($bkStatus) {
			case self::STATUS_CHECKER_APPROVED:
				$this->status = self::STATUS_APPROVER_APPROVED_FIRST;
				break;
			case self::STATUS_APPROVER_APPROVED_FIRST:
				$this->status = self::STATUS_APPROVER_APPROVED_SECOND;
				break;
			case self::STATUS_APPROVER_APPROVED_SECOND:
				$this->status = self::STATUS_APPROVER_APPROVED_THIRD;
				break;
		}
		$result       = $this->getApproverToAssign($approvalAmountPolicy);
		$this->status = $bkStatus;
		if(empty($result['approverEmployee']) && empty($result['approverBackupEmployee'])) {
			return null;
		}
		
		return $result;
	}
	
	public function getApproverToAssign(ApprovalAmountPolicies $approvalAmountPolicy = null) {
		if(empty($approvalAmountPolicy)) {
			if(empty($this->approver)) {
				return [ 'approverEmployee' => null, 'approverBackupEmployee' => null ];
			}
			$approvalAmountPolicy = $this->approver;
		}
		$claim  = $this;
		$amount = $claim->getClaimAmount();
		
		
		// to support multi-line Approval Workflow
		if($approvalAmountPolicy->getApprover2() && $claim->getStatus() === Claim::STATUS_APPROVER_APPROVED_FIRST) {
			if($approvalAmountPolicy->getApprover2()->getId() != $this->getPosition()->getId()) {
				$result['approverEmployee'] = $approvalAmountPolicy->getApprover2();
			} else {
				$result['approverEmployee'] = $approvalAmountPolicy->getOverrideApprover2();
			}
			$result['approverBackupEmployee'] = $approvalAmountPolicy->getBackupApprover2();
			
			return $result;
		} elseif($approvalAmountPolicy->getApprover3() && $claim->getStatus() === Claim::STATUS_APPROVER_APPROVED_SECOND) {
			if($approvalAmountPolicy->getApprover3()->getId() != $this->getPosition()->getId()) {
				$result['approverEmployee'] = $approvalAmountPolicy->getApprover3();
			} else {
				$result['approverEmployee'] = $approvalAmountPolicy->getOverrideApprover3();
			}
			$result['approverBackupEmployee'] = $approvalAmountPolicy->getBackupApprover3();
			
			return $result;
		} elseif($claim->getStatus() === Claim::STATUS_APPROVER_APPROVED) {
			return [
				'approverEmployee'       => $claim->getApproverEmployee(),
				'approverBackupEmployee' => $claim->getApproverBackupEmployee()
			];
		}
		
		//check approver1 can approve ?
		if($approvalAmountPolicy->getApprover1() && $approvalAmountPolicy->isApproval1AmountStatus()) {
			if($approvalAmountPolicy->getApproval1Amount()) {
				if($approvalAmountPolicy->getApproval1Amount() >= $amount) {
					if($approvalAmountPolicy->getApprover1()->getId() != $this->getPosition()->getId()) {
						$result['approverEmployee'] = $approvalAmountPolicy->getApprover1();
					} else {
						$result['approverEmployee'] = $approvalAmountPolicy->getOverrideApprover1();
					}
					$result['approverBackupEmployee'] = $approvalAmountPolicy->getBackupApprover1();
					
					return $result;
				}
			} else {
				if($approvalAmountPolicy->getApprover1()->getId() != $this->getPosition()->getId()) {
					$result['approverEmployee'] = $approvalAmountPolicy->getApprover1();
				} else {
					$result['approverEmployee'] = $approvalAmountPolicy->getOverrideApprover1();
				}
				$result['approverBackupEmployee'] = $approvalAmountPolicy->getBackupApprover1();
				
				return $result;
			}
		}
		//check approver2 can approve ?
		if($approvalAmountPolicy->getApprover2() && $approvalAmountPolicy->isApproval2AmountStatus()) {
			if($approvalAmountPolicy->getApproval2Amount()) {
				if($approvalAmountPolicy->getApproval2Amount() >= $amount) {
					if($approvalAmountPolicy->getApprover2()->getId() != $this->getPosition()->getId()) {
						$result['approverEmployee'] = $approvalAmountPolicy->getApprover2();
					} else {
						$result['approverEmployee'] = $approvalAmountPolicy->getOverrideApprover2();
					}
					$result['approverBackupEmployee'] = $approvalAmountPolicy->getBackupApprover2();
					
					return $result;
				}
			} else {
				if($approvalAmountPolicy->getApprover2()->getId() != $this->getPosition()->getId()) {
					$result['approverEmployee'] = $approvalAmountPolicy->getApprover2();
				} else {
					$result['approverEmployee'] = $approvalAmountPolicy->getOverrideApprover2();
				}
				$result['approverBackupEmployee'] = $approvalAmountPolicy->getBackupApprover2();
				
				return $result;
			}
		}
		
		//check approver3 can approve ?
		if($approvalAmountPolicy->getApprover3() && $approvalAmountPolicy->isApproval3AmountStatus()) {
			if($approvalAmountPolicy->getApproval3Amount()) {
				if($approvalAmountPolicy->getApproval3Amount() >= $amount) {
					if($approvalAmountPolicy->getApprover3()->getId() != $this->getPosition()->getId()) {
						$result['approverEmployee'] = $approvalAmountPolicy->getApprover3();
					} else {
						$result['approverEmployee'] = $approvalAmountPolicy->getOverrideApprover3();
					}
					$result['approverBackupEmployee'] = $approvalAmountPolicy->getBackupApprover3();
					
					return $result;
				}
			} else {
				if($approvalAmountPolicy->getApprover3()->getId() != $this->getPosition()->getId()) {
					$result['approverEmployee'] = $approvalAmountPolicy->getApprover3();
				} else {
					$result['approverEmployee'] = $approvalAmountPolicy->getOverrideApprover3();
				}
				$result['approverBackupEmployee'] = $approvalAmountPolicy->getBackupApprover3();
				
				return $result;
			}
		}
		
		return [ 'approverEmployee' => null, 'approverBackupEmployee' => null ];
		
	}
	
	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return boolean
	 */
	public function isFlexiClaim() {
		return $this->flexiClaim;
	}
	
	/**
	 * @param boolean $flexiClaim
	 */
	public function setFlexiClaim($flexiClaim) {
		$this->flexiClaim = $flexiClaim;
	}
	
	/**
	 * @return string
	 */
	public function getApproverRemark() {
		return $this->approverRemark;
	}
	
	/**
	 * @param string $approverRemark
	 */
	public function setApproverRemark($approverRemark) {
		$this->approverRemark = $approverRemark;
	}
	
	
	/**
	 * @return CheckerHistory
	 */
	public function getApproverHistories() {
		return $this->approverHistories;
	}
	
	/**
	 * @param CheckerHistory $approverHistories
	 */
	public function setApproverHistories($approverHistories) {
		$this->approverHistories = $approverHistories;
	}
	
	
	/**
	 * @return CheckerHistory
	 */
	public function getCheckingHistories() {
		return $this->checkingHistories;
	}
	
	/**
	 * @param CheckerHistory $checkingHistories
	 */
	public function setCheckingHistories($checkingHistories) {
		$this->checkingHistories = $checkingHistories;
	}
	
	
	/**
	 * @return PayCode
	 */
	public function getPayCode() {
		return $this->payCode;
	}
	
	/**
	 * @param PayCode $payCode
	 */
	public function setPayCode($payCode) {
		$this->payCode = $payCode;
	}
	
	
	/**
	 * @return Date
	 */
	public function getProcessedDate() {
		return $this->processedDate;
	}
	
	/**
	 * @param Date $processedDate
	 */
	public function setProcessedDate($processedDate) {
		$this->processedDate = $processedDate;
	}
	
	
	/**
	 * @return float
	 */
	public function getExRate() {
		return $this->exRate;
	}
	
	/**
	 * @param float $exRate
	 */
	public function setExRate($exRate) {
		$this->exRate = $exRate;
	}
	
	
	/**
	 * @return float
	 */
	public function getClaimAmountConverted() {
		return $this->claimAmountConverted;
	}
	
	/**
	 * @param float $claimAmountConverted
	 */
	public function setClaimAmountConverted($claimAmountConverted) {
		$this->claimAmountConverted = $claimAmountConverted;
	}
	
	/**
	 * @return float
	 */
	public function getTaxAmountConverted() {
		return $this->taxAmountConverted;
	}
	
	/**
	 * @param float $taxAmountConverted
	 */
	public function setTaxAmountConverted($taxAmountConverted) {
		$this->taxAmountConverted = $taxAmountConverted;
	}
	
	
	/**
	 * @return Position
	 */
	public function getCreatedBy() {
		return $this->createdBy;
	}
	
	/**
	 * @param Position $createdBy
	 */
	public function setCreatedBy($createdBy) {
		$this->createdBy = $createdBy;
	}
	
	
	/**
	 * @return Position
	 */
	public function getApproverEmployee() {
		return $this->approverEmployee;
	}
	
	/**
	 * @param Position $approverEmployee
	 */
	public function setApproverEmployee($approverEmployee) {
		$this->approverEmployee = $approverEmployee;
	}
	
	/**
	 * @return Position
	 */
	public function getApproverBackupEmployee() {
		return $this->approverBackupEmployee;
	}
	
	/**
	 * @param Position $approverBackupEmployee
	 */
	public function setApproverBackupEmployee($approverBackupEmployee) {
		$this->approverBackupEmployee = $approverBackupEmployee;
	}
	
	
	/**
	 * @return \DateTime
	 */
	public function getCheckerUpdatedAt() {
		return $this->checkerUpdatedAt;
	}
	
	/**
	 * @param \DateTime $checkerUpdatedAt
	 */
	public function setCheckerUpdatedAt($checkerUpdatedAt) {
		$this->checkerUpdatedAt = $checkerUpdatedAt;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getApproverUpdatedAt() {
		return $this->approverUpdatedAt;
	}
	
	/**
	 * @param \DateTime $approverUpdatedAt
	 */
	public function setApproverUpdatedAt($approverUpdatedAt) {
		$this->approverUpdatedAt = $approverUpdatedAt;
	}
	
	
	/**
	 * @return string
	 */
	public function getCheckerRemark() {
		return $this->checkerRemark;
	}
	
	/**
	 * @param string $checkerRemark
	 */
	public function setCheckerRemark($checkerRemark) {
		$this->checkerRemark = $checkerRemark;
	}
	
	
	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}
	
	/**
	 * @return text
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getPeriodFrom() {
		return $this->periodFrom;
	}
	
	/**
	 * @param \DateTime $periodFrom
	 */
	public function setPeriodFrom($periodFrom) {
		$this->periodFrom = $periodFrom;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getPeriodTo() {
		return $this->periodTo;
	}
	
	/**
	 * @param \DateTime $periodTo
	 */
	public function setPeriodTo($periodTo) {
		$this->periodTo = $periodTo;
	}
	
	
	/**
	 * @return Checker
	 */
	public function getChecker() {
		return $this->checker;
	}
	
	/**
	 * @param Checker $checker
	 */
	public function setChecker($checker) {
		$this->checker = $checker;
	}
	
	/**
	 * @return ApprovalAmountPolicies
	 */
	public function getApprover() {
		return $this->approver;
	}
	
	/**
	 * @param ApprovalAmountPolicies $approver
	 */
	public function setApprover($approver) {
		$this->approver = $approver;
	}
	
	
	/**
	 * @return Company
	 */
	public function getCompany() {
		return $this->company;
	}
	
	/**
	 * @param Company $company
	 */
	public function setCompany($company) {
		$this->company = $company;
	}
	
	
	/**
	 * @return ClaimType
	 */
	public function getClaimType() {
		return $this->claimType;
	}
	
	/**
	 * @param ClaimType $claimType
	 */
	public function setClaimType($claimType) {
		$this->claimType = $claimType;
	}
	
	
	/**
	 * @return ClaimCategory
	 */
	public function getClaimCategory() {
		return $this->claimCategory;
	}
	
	/**
	 * @param ClaimCategory $claimCategory
	 */
	public function setClaimCategory($claimCategory) {
		$this->claimCategory = $claimCategory;
	}
	
	
	/**
	 * @return float
	 */
	public function getClaimAmount() {
		return $this->claimAmount;
	}
	
	/**
	 * @param float $claimAmount
	 */
	public function setClaimAmount($claimAmount) {
		$this->claimAmount = $claimAmount;
	}
	
	
	/**
	 * @return CurrencyExchange
	 */
	public function getCurrencyExchange() {
		return $this->currencyExchange;
	}
	
	/**
	 * @param CurrencyExchange $currencyExchange
	 */
	public function setCurrencyExchange($currencyExchange) {
		$this->currencyExchange = $currencyExchange;
	}
	
	/**
	 * @return date
	 */
	public function getReceiptDate() {
		return $this->receiptDate;
	}
	
	/**
	 * @param date $receiptDate
	 */
	public function setReceiptDate($receiptDate) {
		$this->receiptDate = $receiptDate;
	}
	
	/**
	 * @return string
	 */
	public function getSubmissionRemarks() {
		return $this->submissionRemarks;
	}
	
	/**
	 * @param string $submissionRemarks
	 */
	public function setSubmissionRemarks($submissionRemarks) {
		$this->submissionRemarks = $submissionRemarks;
	}
	
	/**
	 * @return User
	 */
	public function getPosition() {
		return $this->position;
	}
	
	/**
	 * @param User $position
	 */
	public function setPosition($position) {
		$this->position = $position;
	}
	
	
	/**
	 * @return \DateTime
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}
	
	/**
	 * @param \DateTime $createdAt
	 */
	public function setCreatedAt($createdAt) {
		$this->createdAt = $createdAt;
	}
	
	/**
	 * @return TaxRate
	 */
	public function getTaxRate() {
		return $this->taxRate;
	}
	
	/**
	 * @param TaxRate $taxRate
	 */
	public function setTaxRate($taxRate) {
		$this->taxRate = $taxRate;
	}
	
	/**
	 * @return float
	 */
	public function getTaxAmount() {
		return $this->taxAmount;
	}
	
	/**
	 * @param float $taxAmount
	 */
	public function setTaxAmount($taxAmount) {
		$this->taxAmount = $taxAmount;
	}
	
	
	/**
	 * @return ClaimMedia
	 */
	public function getClaimMedias() {
		return $this->claimMedias;
	}
	
	/**
	 * @param ClaimMedia $claimMedias
	 */
	public function setClaimMedias($claimMedias) {
		$this->claimMedias = $claimMedias;
	}
	
	
	public function addClaimMedia($claimMedia) {
		$this->claimMedias->add($claimMedia);
		$claimMedia->setClaim($this);
		
		return $this;
	}
	
	public function removeClaimMedia($claimMedia) {
		$this->claimMedias->removeElement($claimMedia);
		$claimMedia->setClaim(null);
	}
	
	/**
	 * @return CompanyClaimPolicies|null
	 */
	public function getClaimPolicy() {
		$company       = $this->getCompany();
		$clientCompany = $company->getParent() ? $company->getParent() : $company;
		$claimPolicies = $clientCompany->getCompanyClaimPolicies();
		if($claimPolicies->count()) {
			return $claimPolicies[0];
		}
		
		return null;
	}
	
	public function setPeriod() {
		if($this->getClaimType()) {
			$claimPolicy = $this->getClaimPolicy();
			if($claimPolicy) {
				$cutOffDay   = $claimPolicy->getCutOffDate();
				$currentDate = new \DateTime();
				$cutOffDate  = new \DateTime($currentDate->format('Y-m-') . $cutOffDay);
				
				if($currentDate <= $cutOffDate) {
					$periodTo   = clone $cutOffDate;
					$clone      = clone $periodTo;
					$periodFrom = $clone->modify('-1 month')->modify(" +1 day");
				} else {
					$periodTo = clone $cutOffDate;
					$periodTo->modify('+1 month');
					$clone      = clone $periodTo;
					$periodFrom = $clone->modify('-1 month')->modify(" +1 day");
				}

//				$periodFrom->setDate($periodFrom->format('Y'), $periodFrom->format('m'), $cutOffDay + 1);
				$this->setPeriodFrom($periodFrom);
				$this->setPeriodTo($periodTo);
			}
		}
	}
	
	public function validate(ExecutionContextInterface $context, $payload) {
		$this->setPeriod();
		if($this->getReceiptDate() && $this->getPeriodFrom() && $this->getPeriodTo()) {
			$claimPolicy = $this->getClaimPolicy();
			$claimable   = $claimPolicy->getClaimablePeriod();
			$to          = date('Y-m-d');
			$from        = date('Y-m-d', strtotime('- ' . $claimable . ' month', strtotime($to)));
			if($this->getReceiptDate()->getTimestamp() < strtotime($from) || $this->getReceiptDate()->getTimestamp() > strtotime($to)) {
				$context->buildViolation('This receipt date is invalid')
				        ->atPath('receiptDate')
				        ->addViolation();
			}
		}
		
	}
	
	
}