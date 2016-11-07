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
class Claim
{

    const STATUS_NOT_USE = 'NOT_USE';
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_PENDING = 'PENDING';
    const STATUS_CHECKER_APPROVED = 'CHECKER_APPROVED';
    const STATUS_CHECKER_REJECTED = 'CHECKER_REJECTED';
    const STATUS_APPROVER_APPROVED = 'APPROVER_APPROVED';
    const STATUS_APPROVER_REJECTED = 'APPROVER_REJECTED';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     */
    private $company;


    /**
     * @var ClaimType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClaimType")
     */
    private $claimType;
    /**
     * @var CurrencyExchange
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CurrencyExchange")
     */
    private $currencyExchange;
    /**
     * @var ClaimCategory
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClaimCategory")
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
     * @var string
     * @ORM\Column(name="submission_remarks",type="text",nullable=true)
     */
    private $submissionRemarks;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position",inversedBy="claims")
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
     */
    private $checker;

    /**
     * @var ApprovalAmountPolicies
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ApprovalAmountPolicies",cascade={"persist"},inversedBy="claims")
     */
    private $approver;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     */
    private $approverEmployee;
    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     */
    private $approverBackupEmployee;

    /**
     * @var TaxRate
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TaxRate")
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
     * @var Date
     * @ORM\Column(name="period_from",type="date",nullable=true)
     */
    private $periodFrom;
    /**
     * @var Date
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

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->claimMedias = new ArrayCollection();
        $this->status = self::STATUS_NOT_USE;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getExRate()
    {
        return $this->exRate;
    }

    /**
     * @param float $exRate
     */
    public function setExRate($exRate)
    {
        $this->exRate = $exRate;
    }



    /**
     * @return float
     */
    public function getClaimAmountConverted()
    {
        return $this->claimAmountConverted;
    }

    /**
     * @param float $claimAmountConverted
     */
    public function setClaimAmountConverted($claimAmountConverted)
    {
        $this->claimAmountConverted = $claimAmountConverted;
    }

    /**
     * @return float
     */
    public function getTaxAmountConverted()
    {
        return $this->taxAmountConverted;
    }

    /**
     * @param float $taxAmountConverted
     */
    public function setTaxAmountConverted($taxAmountConverted)
    {
        $this->taxAmountConverted = $taxAmountConverted;
    }



    /**
     * @return Position
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param Position $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }



    /**
     * @return Position
     */
    public function getApproverEmployee()
    {
        return $this->approverEmployee;
    }

    /**
     * @param Position $approverEmployee
     */
    public function setApproverEmployee($approverEmployee)
    {
        $this->approverEmployee = $approverEmployee;
    }

    /**
     * @return Position
     */
    public function getApproverBackupEmployee()
    {
        return $this->approverBackupEmployee;
    }

    /**
     * @param Position $approverBackupEmployee
     */
    public function setApproverBackupEmployee($approverBackupEmployee)
    {
        $this->approverBackupEmployee = $approverBackupEmployee;
    }



    /**
     * @return \DateTime
     */
    public function getCheckerUpdatedAt()
    {
        return $this->checkerUpdatedAt;
    }

    /**
     * @param \DateTime $checkerUpdatedAt
     */
    public function setCheckerUpdatedAt($checkerUpdatedAt)
    {
        $this->checkerUpdatedAt = $checkerUpdatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getApproverUpdatedAt()
    {
        return $this->approverUpdatedAt;
    }

    /**
     * @param \DateTime $approverUpdatedAt
     */
    public function setApproverUpdatedAt($approverUpdatedAt)
    {
        $this->approverUpdatedAt = $approverUpdatedAt;
    }



    /**
     * @return string
     */
    public function getCheckerRemark()
    {
        return $this->checkerRemark;
    }

    /**
     * @param string $checkerRemark
     */
    public function setCheckerRemark($checkerRemark)
    {
        $this->checkerRemark = $checkerRemark;
    }


    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return text
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return Date
     */
    public function getPeriodFrom()
    {
        return $this->periodFrom;
    }

    /**
     * @param Date $periodFrom
     */
    public function setPeriodFrom($periodFrom)
    {
        $this->periodFrom = $periodFrom;
    }

    /**
     * @return Date
     */
    public function getPeriodTo()
    {
        return $this->periodTo;
    }

    /**
     * @param Date $periodTo
     */
    public function setPeriodTo($periodTo)
    {
        $this->periodTo = $periodTo;
    }


    /**
     * @return Checker
     */
    public function getChecker()
    {
        return $this->checker;
    }

    /**
     * @param Checker $checker
     */
    public function setChecker($checker)
    {
        $this->checker = $checker;
    }

    /**
     * @return ApprovalAmountPolicies
     */
    public function getApprover()
    {
        return $this->approver;
    }

    /**
     * @param ApprovalAmountPolicies $approver
     */
    public function setApprover($approver)
    {
        $this->approver = $approver;
    }


    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Company $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }


    /**
     * @return ClaimType
     */
    public function getClaimType()
    {
        return $this->claimType;
    }

    /**
     * @param ClaimType $claimType
     */
    public function setClaimType($claimType)
    {
        $this->claimType = $claimType;
    }


    /**
     * @return ClaimCategory
     */
    public function getClaimCategory()
    {
        return $this->claimCategory;
    }

    /**
     * @param ClaimCategory $claimCategory
     */
    public function setClaimCategory($claimCategory)
    {
        $this->claimCategory = $claimCategory;
    }


    /**
     * @return float
     */
    public function getClaimAmount()
    {
        return $this->claimAmount;
    }

    /**
     * @param float $claimAmount
     */
    public function setClaimAmount($claimAmount)
    {
        $this->claimAmount = $claimAmount;
    }


    /**
     * @return CurrencyExchange
     */
    public function getCurrencyExchange()
    {
        return $this->currencyExchange;
    }

    /**
     * @param CurrencyExchange $currencyExchange
     */
    public function setCurrencyExchange($currencyExchange)
    {
        $this->currencyExchange = $currencyExchange;
    }

    /**
     * @return date
     */
    public function getReceiptDate()
    {
        return $this->receiptDate;
    }

    /**
     * @param date $receiptDate
     */
    public function setReceiptDate($receiptDate)
    {
        $this->receiptDate = $receiptDate;
    }

    /**
     * @return string
     */
    public function getSubmissionRemarks()
    {
        return $this->submissionRemarks;
    }

    /**
     * @param string $submissionRemarks
     */
    public function setSubmissionRemarks($submissionRemarks)
    {
        $this->submissionRemarks = $submissionRemarks;
    }

    /**
     * @return User
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param User $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }


    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return TaxRate
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * @param TaxRate $taxRate
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;
    }

    /**
     * @return float
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     * @param float $taxAmount
     */
    public function setTaxAmount($taxAmount)
    {
        $this->taxAmount = $taxAmount;
    }


    /**
     * @return ClaimMedia
     */
    public function getClaimMedias()
    {
        return $this->claimMedias;
    }

    /**
     * @param ClaimMedia $claimMedias
     */
    public function setClaimMedias($claimMedias)
    {
        $this->claimMedias = $claimMedias;
    }


    public function addClaimMedia($claimMedia)
    {
        $this->claimMedias->add($claimMedia);
        $claimMedia->setClaim($this);
        return $this;
    }

    public function removeClaimMedia($claimMedia)
    {
        $this->claimMedias->removeElement($claimMedia);
        $claimMedia->setClaim(null);
    }


    public function setPeriod()
    {
        if ($this->getClaimType()) {
            $claimPolicy = $this->getClaimType()->getCompanyClaimPolicies();
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
                $this->setPeriodFrom($periodFrom);
                $this->setPeriodTo($periodTo);
            }
        }
    }

    public function validate(ExecutionContextInterface $context, $payload)
    {
        $this->setPeriod();
        if ($this->getReceiptDate() && $this->getPeriodFrom() && $this->getPeriodTo()) {
            $to = $this->getPeriodTo();
            $clone = clone $to;
            $from = $clone->modify('-' . $this->getClaimType()->getCompanyClaimPolicies()->getClaimablePeriod() . ' month +1 day');
            if ($this->getReceiptDate() < $from || $this->getReceiptDate() > $to) {
                $context->buildViolation('This receipt date is invalid')
                    ->atPath('receiptDate')
                    ->addViolation();
            }
        }

    }


}