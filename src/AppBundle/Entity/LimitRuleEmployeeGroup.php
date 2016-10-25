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
 * @ORM\Table(name="limit_rule_employee_group")
 */
class LimitRuleEmployeeGroup
{


    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var decimal
     * @ORM\Column(name="claim_limit",type="decimal",precision=11,scale=2,nullable=true)
     */
    private $claimLimit;

    /**
     * @var boolean
     * @ORM\Column(name="limit_per_year",type="boolean",options={"default":false})
     */
    private $limitPerYear;

    /**
     * @var LimitRule
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\LimitRule")
     */
    private $limitRule;

    /**
     * @var LimitRule
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EmployeeGroup")
     */
    private $employeeGroup;

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
     * @return LimitRule
     */
    public function getLimitRule()
    {
        return $this->limitRule;
    }

    /**
     * @param LimitRule $limitRule
     */
    public function setLimitRule($limitRule)
    {
        $this->limitRule = $limitRule;
    }

    /**
     * @return LimitRule
     */
    public function getEmployeeGroup()
    {
        return $this->employeeGroup;
    }

    /**
     * @param LimitRule $employeeGroup
     */
    public function setEmployeeGroup($employeeGroup)
    {
        $this->employeeGroup = $employeeGroup;
    }








}