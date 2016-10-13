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
     */
    private $company;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_date",type="datetime")
     */
    private $createdDate;

    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     */
    private $companySetupApproval;
    /**
     * @var CostCentre
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CostCentre")
     */
    private $costCentre;

    /**
     * @var Region
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Region")
     */
    private $region;

    /**
     * @var Branch
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Branch")
     */
    private $branch;

    /**
     * @var Section
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Department")
     */
    private $department;
    /**
     * @var Section
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Section")
     */
    private $section;

    /**
     * @var EmployeeType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EmployeeType")
     */
    private $employeeType;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     */
    private $approver1;
    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     */
    private $approver2;
    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     */
    private $approver3;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     */
    private $backupApprover1;
    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     */
    private $backupApprover2;
    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     */
    private $backupApprover3;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     */
    private $overrideApprover1;
    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     */
    private $overrideApprover2;
    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
     * @return Media
     */
    public function getCompanySetupApproval()
    {
        return $this->companySetupApproval;
    }

    /**
     * @param Media $companySetupApproval
     */
    public function setCompanySetupApproval($companySetupApproval)
    {
        $this->companySetupApproval = $companySetupApproval;
    }

    /**
     * @return CostCentre
     */
    public function getCostCentre()
    {
        return $this->costCentre;
    }

    /**
     * @param CostCentre $costCentre
     */
    public function setCostCentre($costCentre)
    {
        $this->costCentre = $costCentre;
    }

    /**
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param Region $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return Branch
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param Branch $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return Section
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param Section $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    /**
     * @return Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param Section $section
     */
    public function setSection($section)
    {
        $this->section = $section;
    }

    /**
     * @return EmployeeType
     */
    public function getEmployeeType()
    {
        return $this->employeeType;
    }

    /**
     * @param EmployeeType $employeeType
     */
    public function setEmployeeType($employeeType)
    {
        $this->employeeType = $employeeType;
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











    

}