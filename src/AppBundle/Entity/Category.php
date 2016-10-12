<?php
/**
 * Created by PhpStorm.
 * User: chuyennguyen
 * Date: 8/12/16
 * Time: 6:23 PM
 */

namespace AppBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Entity
 * @ORM\Table(name="category")
 */
class Category
{


    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company",inversedBy="categories")
     */
    private $company;


    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     */
    private $companyGetRule;


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
     * @var Department
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Department")
     */
    private $department;

    /**
     * @var Section
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Section")
     */
    private $section;

    /**
     * @var EmploymentType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EmployeeType")
     */
    private $employeeType;

    /**
     * @var PayCode
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PayCode")
     */
    private $payCode;

    /**
     * @var TaxRate
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TaxRate")
     */
    private $taxRate;


    /**
     * @var ClaimType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClaimType")
     */
    private $claimType;
    /**
     * @var ClaimCategory
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClaimCategory")
     */
    private $claimCategory;

    /**
     * @var decimal
     * @ORM\Column(name="claim_limit",type="decimal",precision=11,scale=2,nullable=true)
     */
    private $claimLimit;


    /**
     * @var string
     * @ORM\Column(name="claim_limit_description",type="string")
     */
    private $claimLimitDescription;

    /**
     * @var boolean
     * @ORM\Column(name="has_claim_limit",type="boolean")
     */
    private $hasClaimLimit;

    /**
     * @var boolean
     * @ORM\Column(name="limit_per_year",type="boolean",options={"default":false})
     */
    private $limitPerYear;

    /**
     * @return boolean
     */
    public function isLimitPerYear()
    {
        return $this->limitPerYear;
    }

    /**
     * @param boolean $limitPerYear
     */
    public function setLimitPerYear($limitPerYear)
    {
        $this->limitPerYear = $limitPerYear;
    }


    /**
     * @return boolean
     */
    public function isHasClaimLimit()
    {
        return $this->hasClaimLimit;
    }

    /**
     * @param boolean $hasClaimLimit
     */
    public function setHasClaimLimit($hasClaimLimit)
    {
        $this->hasClaimLimit = $hasClaimLimit;
    }


    /**
     * @return string
     */
    public function getClaimLimitDescription()
    {
        return $this->claimLimitDescription;
    }

    /**
     * @param string $claimLimitDescription
     */
    public function setClaimLimitDescription($claimLimitDescription)
    {
        $this->claimLimitDescription = $claimLimitDescription;
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
     * @return PayCode
     */
    public function getPayCode()
    {
        return $this->payCode;
    }

    /**
     * @param PayCode $payCode
     */
    public function setPayCode($payCode)
    {
        $this->payCode = $payCode;
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
    public function getCompanyGetRule()
    {
        return $this->companyGetRule;
    }

    /**
     * @param Company $companyGetRule
     */
    public function setCompanyGetRule($companyGetRule)
    {
        $this->companyGetRule = $companyGetRule;
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
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param Department $department
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
     * @return EmploymentType
     */
    public function getEmployeeType()
    {
        return $this->employeeType;
    }

    /**
     * @param EmploymentType $employeeType
     */
    public function setEmployeeType($employeeType)
    {
        $this->employeeType = $employeeType;
    }

    /**
     * @return decimal
     */
    public function getClaimLimit()
    {
        return $this->claimLimit;
    }

    /**
     * @param decimal $claimLimit
     */
    public function setClaimLimit($claimLimit)
    {
        $this->claimLimit = $claimLimit;
    }


    public function validate(ExecutionContextInterface $context, $payload)
    {
        $company = $this->getCompany();
        $this->claimLimitDescription = $this->buildRule($this);
        $expr = Criteria::expr();
        $criteria = Criteria::create();
        $criteria->where($expr->eq('claimLimitDescription', $this->claimLimitDescription))
            ->andWhere($expr->neq('id', $this->id));
        $categories = $company->getCategories()->matching($criteria);
        if (count($categories)) {
            $context->buildViolation('This rule is exist')
                ->addViolation();
        }
    }

    public function buildRule(Category $category)
    {
        $listRule = [];
        $listRule[] = $category->getCompanyGetRule()->getName();
        if ($category->getCostCentre()) {
            $listRule[] = $category->getCostCentre()->getCode();
        }
        if ($category->getRegion()) {
            $listRule[] = $category->getRegion()->getCode();
        }
        if ($category->getBranch()) {
            $listRule[] = $category->getBranch()->getCode();
        }
        if ($category->getDepartment()) {
            $listRule[] = $category->getDepartment()->getCode();
        }
        if ($category->getSection()) {
            $listRule[] = $category->getSection()->getCode();
        }
        if ($category->getEmployeeType()) {
            $listRule[] = $category->getEmployeeType()->getCode();
        }
        if ($category->getClaimType()) {
            $listRule[] = $category->getClaimType()->getCode();
        }
        if ($category->getClaimCategory()) {
            $listRule[] = $category->getClaimCategory()->getCode();
        }

        $listRuleStr = implode('>', $listRule);
        return $listRuleStr;
    }


}