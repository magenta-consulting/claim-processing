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
 * @ORM\Table(name="approval_amount_policies_employee_group")
 */
class ApprovalAmountPoliciesEmployeeGroup
{


    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var ApprovalAmountPolicies
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ApprovalAmountPolicies")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $approvalAmountPolicies;

    /**
     * @var LimitRule
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EmployeeGroup")
     * @ORM\JoinColumn(onDelete="CASCADE")
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
     * @return Checker
     */
    public function getApprovalAmountPolicies()
    {
        return $this->approvalAmountPolicies;
    }

    /**
     * @param Checker $approvalAmountPolicies
     */
    public function setApprovalAmountPolicies($approvalAmountPolicies)
    {
        $this->approvalAmountPolicies = $approvalAmountPolicies;
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