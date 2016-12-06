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
 * @ORM\Table(name="company_flexi_claim_policies")
 */
class CompanyFlexiClaimPolicies
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(name="date_start",type="integer")
     */
    private $dateStart;
    /**
     * @var integer
     * @ORM\Column(name="month_start",type="integer")
     */
    private $monthStart;

    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id",onDelete="CASCADE")
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
     * @return int
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * @param int $dateStart
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
    }

    /**
     * @return int
     */
    public function getMonthStart()
    {
        return $this->monthStart;
    }

    /**
     * @param int $monthStart
     */
    public function setMonthStart($monthStart)
    {
        $this->monthStart = $monthStart;
    }






    

}