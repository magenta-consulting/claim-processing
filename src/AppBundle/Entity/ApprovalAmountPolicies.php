<?php
/**
 * Created by PhpStorm.
 * User: chuyennguyen
 * Date: 8/12/16
 * Time: 6:23 PM
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="approval_amount_policies")
 */
class ApprovalAmountPolicies
{


    public function __construct()
    {
        $this->createdDate = new \DateTime();
        $this->claims = new ArrayCollection();
        $this->approvalAmountPoliciesEmployeeGroups = new ArrayCollection();
    }

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
     * @var \DateTime
     * @ORM\Column(name="created_date",type="datetime")
     */
    private $createdDate;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $approver1;
    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $approver2;
    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $approver3;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $backupApprover1;
    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $backupApprover2;
    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $backupApprover3;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $overrideApprover1;
    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $overrideApprover2;
    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $overrideApprover3;

    /**
     * @var float
     * @ORM\Column(name="approval1amount",type="float",nullable=true)
     */
    private $approval1Amount;
    /**
     * @var float
     * @ORM\Column(name="approval2amount",type="float",nullable=true)
     */
    private $approval2Amount;
    /**
     * @var float
     * @ORM\Column(name="approval3amount",type="float",nullable=true)
     */
    private $approval3Amount;

    /**
     * @var boolean
     * @ORM\Column(name="approval1amount_status",type="boolean")
     */
    private $approval1AmountStatus;

    /**
     * @var boolean
     * @ORM\Column(name="approval2amount_status",type="boolean")
     */
    private $approval2AmountStatus;

    /**
     * @var boolean
     * @ORM\Column(name="approval3amount_status",type="boolean")
     */
    private $approval3AmountStatus;

    /**
     * @var Claim
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Claim",mappedBy="approver")
     */
    private $claims;
    /**
     * @var ApprovalAmountPoliciesEmployeeGroup
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ApprovalAmountPoliciesEmployeeGroup",mappedBy="approvalAmountPolicies",cascade={"all"},orphanRemoval=true)
     */
    private $approvalAmountPoliciesEmployeeGroups;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param \DateTime $createdDate
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    }

    /**
     * @return Position
     */
    public function getApprover1()
    {
        return $this->approver1;
    }

    /**
     * @param Position $approver1
     */
    public function setApprover1($approver1)
    {
        $this->approver1 = $approver1;
    }

    /**
     * @return Position
     */
    public function getApprover2()
    {
        return $this->approver2;
    }

    /**
     * @param Position $approver2
     */
    public function setApprover2($approver2)
    {
        $this->approver2 = $approver2;
    }

    /**
     * @return Position
     */
    public function getApprover3()
    {
        return $this->approver3;
    }

    /**
     * @param Position $approver3
     */
    public function setApprover3($approver3)
    {
        $this->approver3 = $approver3;
    }

    /**
     * @return Position
     */
    public function getBackupApprover1()
    {
        return $this->backupApprover1;
    }

    /**
     * @param Position $backupApprover1
     */
    public function setBackupApprover1($backupApprover1)
    {
        $this->backupApprover1 = $backupApprover1;
    }

    /**
     * @return Position
     */
    public function getBackupApprover2()
    {
        return $this->backupApprover2;
    }

    /**
     * @param Position $backupApprover2
     */
    public function setBackupApprover2($backupApprover2)
    {
        $this->backupApprover2 = $backupApprover2;
    }

    /**
     * @return Position
     */
    public function getBackupApprover3()
    {
        return $this->backupApprover3;
    }

    /**
     * @param Position $backupApprover3
     */
    public function setBackupApprover3($backupApprover3)
    {
        $this->backupApprover3 = $backupApprover3;
    }

    /**
     * @return Position
     */
    public function getOverrideApprover1()
    {
        return $this->overrideApprover1;
    }

    /**
     * @param Position $overrideApprover1
     */
    public function setOverrideApprover1($overrideApprover1)
    {
        $this->overrideApprover1 = $overrideApprover1;
    }

    /**
     * @return Position
     */
    public function getOverrideApprover2()
    {
        return $this->overrideApprover2;
    }

    /**
     * @param Position $overrideApprover2
     */
    public function setOverrideApprover2($overrideApprover2)
    {
        $this->overrideApprover2 = $overrideApprover2;
    }

    /**
     * @return Position
     */
    public function getOverrideApprover3()
    {
        return $this->overrideApprover3;
    }

    /**
     * @param Position $overrideApprover3
     */
    public function setOverrideApprover3($overrideApprover3)
    {
        $this->overrideApprover3 = $overrideApprover3;
    }

    /**
     * @return float
     */
    public function getApproval1Amount()
    {
        return $this->approval1Amount;
    }

    /**
     * @param float $approval1Amount
     */
    public function setApproval1Amount($approval1Amount)
    {
        $this->approval1Amount = $approval1Amount;
    }

    /**
     * @return float
     */
    public function getApproval2Amount()
    {
        return $this->approval2Amount;
    }

    /**
     * @param float $approval2Amount
     */
    public function setApproval2Amount($approval2Amount)
    {
        $this->approval2Amount = $approval2Amount;
    }

    /**
     * @return float
     */
    public function getApproval3Amount()
    {
        return $this->approval3Amount;
    }

    /**
     * @param float $approval3Amount
     */
    public function setApproval3Amount($approval3Amount)
    {
        $this->approval3Amount = $approval3Amount;
    }

    /**
     * @return boolean
     */
    public function isApproval1AmountStatus()
    {
        return $this->approval1AmountStatus;
    }

    /**
     * @param boolean $approval1AmountStatus
     */
    public function setApproval1AmountStatus($approval1AmountStatus)
    {
        $this->approval1AmountStatus = $approval1AmountStatus;
    }

    /**
     * @return boolean
     */
    public function isApproval2AmountStatus()
    {
        return $this->approval2AmountStatus;
    }

    /**
     * @param boolean $approval2AmountStatus
     */
    public function setApproval2AmountStatus($approval2AmountStatus)
    {
        $this->approval2AmountStatus = $approval2AmountStatus;
    }

    /**
     * @return boolean
     */
    public function isApproval3AmountStatus()
    {
        return $this->approval3AmountStatus;
    }

    /**
     * @param boolean $approval3AmountStatus
     */
    public function setApproval3AmountStatus($approval3AmountStatus)
    {
        $this->approval3AmountStatus = $approval3AmountStatus;
    }

    /**
     * @return Claim
     */
    public function getClaims()
    {
        return $this->claims;
    }

    /**
     * @param Claim $claims
     */
    public function setClaims($claims)
    {
        $this->claims = $claims;
    }

    /**
     * @return ApprovalAmountPoliciesEmployeeGroup
     */
    public function getApprovalAmountPoliciesEmployeeGroups()
    {
        return $this->approvalAmountPoliciesEmployeeGroups;
    }

    /**
     * @param ApprovalAmountPoliciesEmployeeGroup $approvalAmountPoliciesEmployeeGroups
     */
    public function setApprovalAmountPoliciesEmployeeGroups($approvalAmountPoliciesEmployeeGroups)
    {
        $this->approvalAmountPoliciesEmployeeGroups = $approvalAmountPoliciesEmployeeGroups;
    }

    public function addApprovalAmountPoliciesEmployeeGroup($approvalAmountPoliciesEmployeeGroup)
    {
        $this->approvalAmountPoliciesEmployeeGroups->add($approvalAmountPoliciesEmployeeGroup);
        $approvalAmountPoliciesEmployeeGroup->setApprovalAmountPolicies($this);
        return $this;
    }

    public function removeApprovalAmountPoliciesEmployeeGroup($approvalAmountPoliciesEmployeeGroup)
    {
        $this->approvalAmountPoliciesEmployeeGroups->removeElement($approvalAmountPoliciesEmployeeGroup);
        $approvalAmountPoliciesEmployeeGroup->setApprovalAmountPolicies(null);
    }

    public function validate(ExecutionContextInterface $context, $payload)
    {
        //1.validate each employee only belong to a approver
        $company = $this->getCompany();
        if ($company) {
            $expr = Criteria::expr();
            $criteria = Criteria::create();
            $criteria->where($expr->neq('id', $this->id));
            $approvalAmountPolicies = $company->getApprovalAmountPolicies()->matching($criteria);
            foreach ($approvalAmountPolicies as $approvalAmountPolicy) {
                foreach ($approvalAmountPolicy->getApprovalAmountPoliciesEmployeeGroups() as $approvalAmountPoliciesEmployeeGroup1) {
                    foreach ($this->getApprovalAmountPoliciesEmployeeGroups() as $approvalAmountPoliciesEmployeeGroup2) {
                        if ($approvalAmountPoliciesEmployeeGroup1->getEmployeeGroup() && $approvalAmountPoliciesEmployeeGroup2->getEmployeeGroup()) {
                            if ($approvalAmountPoliciesEmployeeGroup1->getEmployeeGroup()->getId() == $approvalAmountPoliciesEmployeeGroup2->getEmployeeGroup()->getId()) {

                                $context->buildViolation('This employee group (' . $approvalAmountPoliciesEmployeeGroup2->getEmployeeGroup()->getDescription() . ') has already been belong to another approval amount policy')
                                    ->atPath('approvalAmountPoliciesEmployeeGroups')
                                    ->addViolation();
                            }
                        }
                    }

                }
            }
        }
        //2. validation for approver1 : approver1 is required, and approver,backup,overide must be difference
        if(!$this->getApprover1()){
            $context->buildViolation('Approver 1 is required')
                ->atPath('approver1')
                ->addViolation();
        }
        if($this->getBackupApprover1() && $this->getApprover1()){
            if($this->getBackupApprover1()->getId() === $this->getApprover1()->getId()){
                $context->buildViolation('Backup Approver must be difference with Approver')
                    ->atPath('backupApprover1')
                    ->addViolation();
            }
        }
        if($this->getOverrideApprover1() && $this->getApprover1()){
            if($this->getOverrideApprover1()->getId() === $this->getApprover1()->getId()){
                $context->buildViolation('Override Approver must be difference with Approver')
                    ->atPath('overrideApprover1')
                    ->addViolation();
            }
        }

        //3 validation for approver2 : must input approver first, and approver,backup,overide must be difference
        if(($this->getApproval2Amount() || $this->getOverrideApprover2()) && $this->getApprover2() === null){
                $context->buildViolation('Must input approver first')
                    ->atPath('approver2')
                    ->addViolation();
        }
        if($this->getBackupApprover2() && $this->getApprover2()){
            if($this->getBackupApprover2()->getId() === $this->getApprover2()->getId()){
                $context->buildViolation('Backup Approver must be difference with Approver')
                    ->atPath('backupApprover2')
                    ->addViolation();
            }
        }
        if($this->getOverrideApprover2() && $this->getApprover2()){
            if($this->getOverrideApprover2()->getId() === $this->getApprover2()->getId()){
                $context->buildViolation('Override Approver must be difference with Approver')
                    ->atPath('overrideApprover2')
                    ->addViolation();
            }
        }

        //4 validation for approver3 : must input approver first, and approver,backup,overide must be difference
        if(($this->getApproval3Amount() || $this->getOverrideApprover3()) && $this->getApprover3() === null){
                $context->buildViolation('Must input approver first')
                    ->atPath('approver3')
                    ->addViolation();
        }
        if($this->getBackupApprover3() && $this->getApprover3()){
            if($this->getBackupApprover3()->getId() === $this->getApprover3()->getId()){
                $context->buildViolation('Backup Approver must be difference with Approver')
                    ->atPath('backupApprover3')
                    ->addViolation();
            }
        }
        if($this->getOverrideApprover3() && $this->getApprover3()){
            if($this->getOverrideApprover3()->getId() === $this->getApprover3()->getId()){
                $context->buildViolation('Override Approver must be difference with Approver')
                    ->atPath('overrideApprover3')
                    ->addViolation();
            }
        }

    }


}