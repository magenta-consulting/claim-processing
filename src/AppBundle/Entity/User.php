<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{


    public function __construct()
    {
        $this->createdDate = new \DateTime();
        parent::__construct();
        // your own logic
    }
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /** @var string
     * @ORM\Column(name="first_name",type="string",nullable=true)
     */
    private $firstName;

    /** @var string
     * @ORM\Column(name="last_name",type="string",nullable=true)
     */
    private $lastName;


    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media",cascade={"persist","remove"})
     */
    private $image;

    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     */
    private $company;

    /**
     * @var string
     * @ORM\Column(name="alias",type="string",nullable=true)
     */
    private $alias;

    /**
     * @var integer
     * @ORM\Column(name="contact_number",type="integer",nullable=true)
     */
    private $contactNumber;
    /**
     * @var integer
     * @ORM\Column(name="employee_no",type="integer")
     */
    private $employeeNo;

    /**
     * @var integer
     * @ORM\Column(name="nric",type="integer")
     */
    private $nric;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_date",type="datetime")
     */
    private $createdDate;

    /**
     * @var EmployeeType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EmployeeType")
     */
    private $employeeType;

    /**
     * @var EmploymentType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\EmploymentType")
     */
    private $employmentType;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_joined",type="date")
     */
    private $dateJoined;

    /**
     * @var float
     * @ORM\Column(name="probation",type="float")
     */
    private $probation;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_date_of_service",type="date")
     */
    private $lastDateOfService;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Section")
     */
    private $section;

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return int
     */
    public function getContactNumber()
    {
        return $this->contactNumber;
    }

    /**
     * @param int $contactNumber
     */
    public function setContactNumber($contactNumber)
    {
        $this->contactNumber = $contactNumber;
    }

    /**
     * @return int
     */
    public function getEmployeeNo()
    {
        return $this->employeeNo;
    }

    /**
     * @param int $employeeNo
     */
    public function setEmployeeNo($employeeNo)
    {
        $this->employeeNo = $employeeNo;
    }



    /**
     * @return int
     */
    public function getNric()
    {
        return $this->nric;
    }

    /**
     * @param int $nric
     */
    public function setNric($nric)
    {
        $this->nric = $nric;
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
     * @return EmployeeType
     */
    public function getEmployeeType()
    {
        return $this->employeeType;
    }

    /**
     * @param EmployeeType $employeeType
     */
    public function setEmployeeType($employeeType)
    {
        $this->employeeType = $employeeType;
    }

    /**
     * @return EmploymentType
     */
    public function getEmploymentType()
    {
        return $this->employmentType;
    }

    /**
     * @param EmploymentType $employmentType
     */
    public function setEmploymentType($employmentType)
    {
        $this->employmentType = $employmentType;
    }

    /**
     * @return \DateTime
     */
    public function getDateJoined()
    {
        return $this->dateJoined;
    }

    /**
     * @param \DateTime $dateJoined
     */
    public function setDateJoined($dateJoined)
    {
        $this->dateJoined = $dateJoined;
    }

    /**
     * @return float
     */
    public function getProbation()
    {
        return $this->probation;
    }

    /**
     * @param float $probation
     */
    public function setProbation($probation)
    {
        $this->probation = $probation;
    }

    /**
     * @return \DateTime
     */
    public function getLastDateOfService()
    {
        return $this->lastDateOfService;
    }

    /**
     * @param \DateTime $lastDateOfService
     */
    public function setLastDateOfService($lastDateOfService)
    {
        $this->lastDateOfService = $lastDateOfService;
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param Media $image
     */
    public function setImage($image)
    {
        $this->image = $image;
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






}