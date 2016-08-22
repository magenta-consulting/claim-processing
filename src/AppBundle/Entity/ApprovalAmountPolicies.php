<?php
/**
 * Created by PhpStorm.
 * User: chuyennguyen
 * Date: 8/12/16
 * Time: 6:23 PM
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="approval_amount_policies")
 */
class ApprovalAmountPolicies
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var CostCentre
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CostCentre")
     */
    private $costCentre;
    /**
     * @var ClaimType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClaimType")
     */
    private $claimType;

    /**
     * @var float
     * @ORM\Column(name="approval1max",type="float",nullable=true)
     */
    private $approval1Max;
    /**
     * @var float
     * @ORM\Column(name="approval2max",type="float",nullable=true)
     */
    private $approval2Max;
    /**
     * @var float
     * @ORM\Column(name="approval3max",type="float",nullable=true)
     */
    private $approval3Max;

    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     */
    private $company;

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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return string
     */
    public function getApproval1Max()
    {
        return $this->approval1Max;
    }

    /**
     * @param string $approval1Max
     */
    public function setApproval1Max($approval1Max)
    {
        $this->approval1Max = $approval1Max;
    }

    /**
     * @return string
     */
    public function getApproval2Max()
    {
        return $this->approval2Max;
    }

    /**
     * @param string $approval2Max
     */
    public function setApproval2Max($approval2Max)
    {
        $this->approval2Max = $approval2Max;
    }

    /**
     * @return string
     */
    public function getApproval3Max()
    {
        return $this->approval3Max;
    }

    /**
     * @param string $approval3Max
     */
    public function setApproval3Max($approval3Max)
    {
        $this->approval3Max = $approval3Max;
    }

    

}