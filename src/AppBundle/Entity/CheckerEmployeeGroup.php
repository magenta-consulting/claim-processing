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
 * @ORM\Table(name="checker_employee_group")
 */
class CheckerEmployeeGroup
{


    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var Checker
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Checker")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $checker;

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