<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="checker")
 */
class Checker
{

    public function __construct()
    {
        $this->createdDate = new \DateTime();
        $this->claims = new ArrayCollection();
        // your own logic
    }


    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     */
    private $company;


    /**
     * @var \DateTime
     * @ORM\Column(name="created_date",type="datetime")
     */
    private $createdDate;

    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     */
    private $companySetupChecker;
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
     * @var Section
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Department")
     */
    private $department;
    /**
     * @var Section
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Section")
     */
    private $section;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     */
    private $checker;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     */
    private $backupChecker;

    /**
     * @var Claim
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Claim",mappedBy="checker")
     */
    private $claims;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Claim
     */
    public function getClaims()
    {
        return $this->claims;
    }

    /**
     * @param Claim $claims
     */
    public function setClaims($claims)
    {
        $this->claims = $claims;
    }

    /**
     * @return Media
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Media $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * @param \DateTime $createdDate
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
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
     * @return User
     */
    public function getChecker()
    {
        return $this->checker;
    }

    /**
     * @param User $checker
     */
    public function setChecker($checker)
    {
        $this->checker = $checker;
    }

    /**
     * @return User
     */
    public function getBackupChecker()
    {
        return $this->backupChecker;
    }

    /**
     * @param User $backupChecker
     */
    public function setBackupChecker($backupChecker)
    {
        $this->backupChecker = $backupChecker;
    }

    /**
     * @return Media
     */
    public function getCompanySetupChecker()
    {
        return $this->companySetupChecker;
    }

    /**
     * @param Media $companySetupChecker
     */
    public function setCompanySetupChecker($companySetupChecker)
    {
        $this->companySetupChecker = $companySetupChecker;
    }

    /**
     * @return Section
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param Section $department
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    }

    public function validate(ExecutionContextInterface $context, $payload)
    {
        if($this->getBackupChecker()) {
            if($this->getChecker()){
                if($this->getBackupChecker()->getId() === $this->getChecker()->getId()) {
                    $context->buildViolation('Backup Checker must be difference with checker')
                        ->atPath('backupChecker')
                        ->addViolation();
                }
            }
        }
    }

















}