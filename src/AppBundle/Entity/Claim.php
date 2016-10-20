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
     * @var Company
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     */
    private $companyGetClaim;

    /**
     * @var ClaimType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClaimType")
     */
    private $claimType;
    /**
     * @var ClaimType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClaimType")
     */
    private $claimTypeCurrent;
    /**
     * @var ClaimCategory
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClaimCategory")
     */
    private $claimCategory;

    /**
     * @var bool
     * @ORM\Column(name="gst",type="boolean",options={"default":false})
     */
    private $gst;

    /**
     * @var float
     * @ORM\Column(name="claim_amount",type="float",nullable=true)
     */
    private $claimAmount;
    /**
     * @var float
     * @ORM\Column(name="gst_amount",type="float",nullable=true)
     */
    private $gstAmount;
    /**
     * @var float
     * @ORM\Column(name="amount_without_gst",type="float",nullable=true)
     */
    private $amountWithoutGst;
    /**
     * @var CurrencyExchange
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CurrencyExchange")
     */
    private $currencyExchange;

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
     * @var Date
     * @ORM\Column(name="period_from",type="date")
     */
    private $periodFrom;
    /**
     * @var Date
     * @ORM\Column(name="period_to",type="date")
     */
    private $periodTo;


    /**
     * @var \DateTime
     * @ORM\Column(name="created_at",type="datetime")
     */
    private $createdAt;

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

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->claimMedias = new ArrayCollection();
        $this->status = self::STATUS_PENDING;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
     * @return Company
     */
    public function getCompanyGetClaim()
    {
        return $this->companyGetClaim;
    }

    /**
     * @param Company $companyGetClaim
     */
    public function setCompanyGetClaim($companyGetClaim)
    {
        $this->companyGetClaim = $companyGetClaim;
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
     * @return ClaimType
     */
    public function getClaimTypeCurrent()
    {
        return $this->claimTypeCurrent;
    }

    /**
     * @param ClaimType $claimTypeCurrent
     */
    public function setClaimTypeCurrent($claimTypeCurrent)
    {
        $this->claimTypeCurrent = $claimTypeCurrent;
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
     * @return boolean
     */
    public function isGst()
    {
        return $this->gst;
    }

    /**
     * @param boolean $gst
     */
    public function setGst($gst)
    {
        $this->gst = $gst;
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
     * @return float
     */
    public function getGstAmount()
    {
        return $this->gstAmount;
    }

    /**
     * @param float $gstAmount
     */
    public function setGstAmount($gstAmount)
    {
        $this->gstAmount = $gstAmount;
    }

    /**
     * @return float
     */
    public function getAmountWithoutGst()
    {
        return $this->amountWithoutGst;
    }

    /**
     * @param float $amountWithoutGst
     */
    public function setAmountWithoutGst($amountWithoutGst)
    {
        $this->amountWithoutGst = $amountWithoutGst;
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
            $this->setClaimTypeCurrent($this->getClaimType());
            $claimPolicy = $this->getClaimType()->getCompanyClaimPolicies();
            if ($claimPolicy) {
                $claimable = $claimPolicy->getClaimablePeriod();
                $cutOffdate = $claimPolicy->getCutOffDate();
                $currentDate = date('d');
                if ($currentDate <= $cutOffdate) {
                    $periodTo = new \DateTime('NOW');
                    $clone = clone $periodTo;
                    $periodFrom = $clone->modify('-' . $claimable . ' month');
                } else {
                    $periodTo = new \DateTime('NOW');
                    $periodTo->modify('+1 month');
                    $clone = clone $periodTo;
                    $periodFrom = $clone->modify('-' . $claimable . ' month');
                }
                $periodFrom->setDate($periodFrom->format('Y'),$periodFrom->format('m'),$cutOffdate+1);
                $periodTo->setDate($periodTo->format('Y'),$periodTo->format('m'),$cutOffdate);
                $this->setPeriodFrom($periodFrom);
                $this->setPeriodTo($periodTo);
            }
        }
    }

    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->id === null) {
            $this->setPeriod();
        } else {
            if ($this->getClaimType()->getId() !== $this->getClaimTypeCurrent()->getId()) {
                $this->setPeriod();
            }
        }
        if ($this->getPeriodFrom()) {
            if ($this->getReceiptDate() < $this->getPeriodFrom() || $this->getReceiptDate() > $this->getPeriodTo()) {
                $context->buildViolation('This receipt date is invalid')
                    ->atPath('receiptDate')
                    ->addViolation();
            }
        }

    }


}