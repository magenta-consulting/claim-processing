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
 * @ORM\Table(name="claim_type")
 */


class ClaimType
{

    public function __construct()
    {
        $this->isDefault = false;
        $this->enabled = true;
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(name="code",type="string")
     */
    private $code;

    /**
     * @var ClaimTypeType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClaimTypeType")
     */
    private $claimTypeType;

    /**
     * @var boolean
     * @ORM\Column(name="enabled",type="boolean")
     */
    private $enabled;

    /**
     * @var boolean
     * @ORM\Column(name="is_default",type="boolean")
     */
    private $isDefault;
    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     */
    private $company;

    /**
     * @var CompanyClaimPolicies
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\CompanyClaimPolicies",mappedBy="claimType")
     */
    private $companyClaimPolicies;

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
    public function getCompanyClaimPolicies()
    {
        return $this->companyClaimPolicies;
    }

    /**
     * @param mixed $companyClaimPolicies
     */
    public function setCompanyClaimPolicies($companyClaimPolicies)
    {
        $this->companyClaimPolicies = $companyClaimPolicies;
    }



    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return ClaimTypeType
     */
    public function getClaimTypeType()
    {
        return $this->claimTypeType;
    }

    /**
     * @param ClaimTypeType $claimTypeType
     */
    public function setClaimTypeType($claimTypeType)
    {
        $this->claimTypeType = $claimTypeType;
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
     * @return boolean
     */
    public function isIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * @param boolean $isDefault
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    }



    

}