<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="third_party_checker")
 */
class ThirdPartyChecker
{
    const ROLE_DEFAULT = 'ROLE_USER';

    public function __construct()
    {
        $this->createdDate = new \DateTime();
        $this->thirdPartyCheckerClients = new ArrayCollection();
        // your own logic
    }
    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->firstName;
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="alias",type="string",nullable=true)
     */
    private $alias;

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
     * @var string
     * @ORM\Column(name="email",type="string")
     */
    private $email;

    /**
     * @var integer
     * @ORM\Column(name="contact_number",type="phone_number",nullable=true)
     */
    private $contactNumber;

    /**
     * @var integer
     * @ORM\Column(name="nric",type="string",nullable=true)
     */
    private $nric;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_date",type="datetime")
     */
    private $createdDate;


    /**
     * @var \DateTime
     * @ORM\Column(name="date_joined",type="date",nullable=true)
     */
    private $dateJoined;

    /**
     * @var float
     * @ORM\Column(name="probation",type="float",nullable=true)
     */
    private $probation;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_date_of_service",type="date",nullable=true)
     */
    private $lastDateOfService;

    /**
     * @var ThirdPartyCheckerClient
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ThirdPartyCheckerClient",mappedBy="thirdPartyChecker",cascade={"all"},orphanRemoval=true)
     */
    private $thirdPartyCheckerClients;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ThirdPartyCheckerClient
     */
    public function getThirdPartyCheckerClients()
    {
        return $this->thirdPartyCheckerClients;
    }

    /**
     * @param ThirdPartyCheckerClient $thirdPartyCheckerClient
     */
    public function setThirdPartyCheckerClients($thirdPartyCheckerClients)
    {
        $this->thirdPartyCheckerClients = $thirdPartyCheckerClients;
    }


    public function addThirdPartyCheckerClient($thirdPartyCheckerClient)
    {
        $this->thirdPartyCheckerClients->add($thirdPartyCheckerClient);
        $thirdPartyCheckerClient->setThirdPartyChecker($this);
        return $this;
    }

    public function removeThirdPartyCheckerClient($thirdPartyCheckerClient)
    {
        $this->thirdPartyCheckerClients->removeElement($thirdPartyCheckerClient);
        $thirdPartyCheckerClient->setThirdPartyChecker(null);
    }

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
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
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
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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





















}