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
 * @ORM\Table(name="company_claim_policies")
 */
class CompanyClaimPolicies
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var date
     * @ORM\Column(name="cut_off_date",type="date")
     */
    private $cutOffDate;
    /**
     * @var integer
     * @ORM\Column(name="claimable_period",type="integer")
     */
    private $claimablePeriod;
    /**
     * @var boolean
     * @ORM\Column(name="enabled",type="boolean")
     */
    private $enabled;
    /**
     * @var ClaimType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClaimType")
     */
    private $claimType;

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
     * @return date
     */
    public function getCutOffDate()
    {
        return $this->cutOffDate;
    }

    /**
     * @param date $cutOffDate
     */
    public function setCutOffDate($cutOffDate)
    {
        $this->cutOffDate = $cutOffDate;
    }

    /**
     * @return int
     */
    public function getClaimablePeriod()
    {
        return $this->claimablePeriod;
    }

    /**
     * @param int $claimablePeriod
     */
    public function setClaimablePeriod($claimablePeriod)
    {
        $this->claimablePeriod = $claimablePeriod;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
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

    

}