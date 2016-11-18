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
        $this->roles = array();
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
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="positions")
     */
    private $user;

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
     * @ORM\Column(name="roles",type="array")
     */
    private $roles;
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
     * @var Claim
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Claim",mappedBy="position")
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
     * Returns the user roles
     *
     * @return array The roles
     */
    public function getRoles()
    {
        $roles = $this->roles;

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }
    public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }
    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return boolean
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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

















}