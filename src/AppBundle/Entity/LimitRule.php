<?php
/**
 * Created by PhpStorm.
 * User: chuyennguyen
 * Date: 8/12/16
 * Time: 6:23 PM
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Entity
 * @ORM\Table(name="limit_rule")
 */
class LimitRule
{



    public function __construct()
    {
        $this->limitRuleEmployeeGroups = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company",inversedBy="limitRules")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $company;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClaimType",inversedBy="limitRules")
     */
    private $claimType;
    /**
     * @var ClaimCategory
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClaimCategory")
     */
    private $claimCategory;

    /**
     * @var employeeGroup
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LimitRuleEmployeeGroup",mappedBy="limitRule",cascade={"all"},orphanRemoval=true)
     */
    private $limitRuleEmployeeGroups;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
     * @return employeeGroup
     */
    public function getLimitRuleEmployeeGroups()
    {
        return $this->limitRuleEmployeeGroups;
    }

    /**
     * @param employeeGroup $limitRuleEmployeeGroups
     */
    public function setLimitRuleEmployeeGroups($limitRuleEmployeeGroups)
    {
        $this->limitRuleEmployeeGroups = $limitRuleEmployeeGroups;
    }



    public function addLimitRuleEmployeeGroup($limitRuleEmployeeGroups)
    {
        $this->limitRuleEmployeeGroups->add($limitRuleEmployeeGroups);
        $limitRuleEmployeeGroups->setLimitRule($this);
        return $this;
    }

    public function removeLimitRuleEmployeeGroup($limitRuleEmployeeGroups)
    {
        $this->limitRuleEmployeeGroups->removeElement($limitRuleEmployeeGroups);
        $limitRuleEmployeeGroups->setLimitRule(null);
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








}