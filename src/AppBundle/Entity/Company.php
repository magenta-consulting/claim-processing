<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="company")
 */
class Company
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $address;

    /**
     * @ORM\Column(type="integer")
     */
    protected $bizCode;

    /**
     * @ORM\Column(type="string")
     */
    protected $country;

    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company",inversedBy="children")
     */
    private $parent;
    /**
     * @var
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Company",mappedBy="parent",cascade={"remove"})
     */

    private $children;


    /**
     * @var Position
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Position",mappedBy="company")
     */
    private $positions;


    /**
     * @var Category
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LimitRule",mappedBy="company")
     */
    private $limitRules;

    /**
     * @var Category
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ClaimType",mappedBy="company")
     */
    private $claimTypes;
    /**
     * @var TaxRate
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TaxRate",mappedBy="company")
     */
    private $taxRates;

    /**
     * @var EmployeeGroup
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\EmployeeGroup",mappedBy="company")
     */
    private $employeeGroups;
    /**
     * @var Checker
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Checker",mappedBy="company")
     */
    private $checkers;

    /**
     * @var ApprovalAmountPolicies
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ApprovalAmountPolicies",mappedBy="company")
     */
    private $approvalAmountPolicies;

    /**
     * @var ApprovalAmountPolicies
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CompanyClaimPolicies",mappedBy="company")
     */
    private $companyClaimPolicies;

    /**
     * @var CurrencyExchange
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CurrencyExchange",mappedBy="company")
     */
    private $currencyExchanges;

    public function __construct()
    {
        $this->positions = new ArrayCollection();
        $this->limitRules = new ArrayCollection();
        $this->claimTypes = new ArrayCollection();
        $this->employeeGroups = new ArrayCollection();
        $this->taxRates = new ArrayCollection();
        $this->companyClaimPolicies = new ArrayCollection();
        $this->currencyExchanges = new ArrayCollection();
    }

    /**
     * @return CurrencyExchange
     */
    public function getCurrencyExchanges()
    {
        return $this->currencyExchanges;
    }

    /**
     * @param CurrencyExchange $currencyExchanges
     */
    public function setCurrencyExchanges($currencyExchanges)
    {
        $this->currencyExchanges = $currencyExchanges;
    }



    /**
     * @return ApprovalAmountPolicies
     */
    public function getCompanyClaimPolicies()
    {
        return $this->companyClaimPolicies;
    }

    /**
     * @param ApprovalAmountPolicies $companyClaimPolicies
     */
    public function setCompanyClaimPolicies($companyClaimPolicies)
    {
        $this->companyClaimPolicies = $companyClaimPolicies;
    }


    /**
     * @return TaxRate
     */
    public function getTaxRates()
    {
        return $this->taxRates;
    }

    /**
     * @param TaxRate $taxRates
     */
    public function setTaxRates($taxRates)
    {
        $this->taxRates = $taxRates;
    }



    /**
     * @return ApprovalAmountPolicies
     */
    public function getApprovalAmountPolicies()
    {
        return $this->approvalAmountPolicies;
    }

    /**
     * @param ApprovalAmountPolicies $approvalAmountPolicies
     */
    public function setApprovalAmountPolicies($approvalAmountPolicies)
    {
        $this->approvalAmountPolicies = $approvalAmountPolicies;
    }


    /**
     * @return Checker
     */
    public function getCheckers()
    {
        return $this->checkers;
    }

    /**
     * @param Checker $checkers
     */
    public function setCheckers($checkers)
    {
        $this->checkers = $checkers;
    }


    /**
     * @return EmployeeGroup
     */
    public function getEmployeeGroups()
    {
        return $this->employeeGroups;
    }

    /**
     * @param EmployeeGroup $employeeGroups
     */
    public function setEmployeeGroups($employeeGroups)
    {
        $this->employeeGroups = $employeeGroups;
    }


    /**
     * @return Category
     */
    public function getClaimTypes()
    {
        return $this->claimTypes;
    }

    /**
     * @param Category $claimTypes
     */
    public function setClaimTypes($claimTypes)
    {
        $this->claimTypes = $claimTypes;
    }


    /**
     * @return Category
     */
    public function getLimitRules()
    {
        return $this->limitRules;
    }

    /**
     * @param Category $limitRules
     */
    public function setLimitRules($limitRules)
    {
        $this->limitRules = $limitRules;
    }





    /**
     * @return Position
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * @param Position $positions
     */
    public function setPositions($positions)
    {
        $this->positions = $positions;
    }



    /**
     * @return Company
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Company $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getBizCode()
    {
        return $this->bizCode;
    }

    /**
     * @param mixed $bizCode
     */
    public function setBizCode($bizCode)
    {
        $this->bizCode = $bizCode;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }


}
