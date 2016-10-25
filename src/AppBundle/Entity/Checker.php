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
        $this->checkerEmployeeGroups = new ArrayCollection();
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
     * @var CheckerEmployeeGroup
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CheckerEmployeeGroup",mappedBy="checker",cascade={"all"},orphanRemoval=true)
     */
    private $checkerEmployeeGroups;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
     * @return CheckerEmployeeGroup
     */
    public function getCheckerEmployeeGroups()
    {
        return $this->checkerEmployeeGroups;
    }

    /**
     * @param CheckerEmployeeGroup $checkerEmployeeGroups
     */
    public function setCheckerEmployeeGroups($checkerEmployeeGroups)
    {
        $this->checkerEmployeeGroups = $checkerEmployeeGroups;
    }

    public function addCheckerEmployeeGroup($checkerEmployeeGroups)
    {
        $this->checkerEmployeeGroups->add($checkerEmployeeGroups);
        $checkerEmployeeGroups->setChecker($this);
        return $this;
    }

    public function removeCheckerEmployeeGroup($checkerEmployeeGroups)
    {
        $this->checkerEmployeeGroups->removeElement($checkerEmployeeGroups);
        $checkerEmployeeGroups->setChecker(null);
    }


    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->getBackupChecker()) {
            if ($this->getChecker()) {
                if ($this->getBackupChecker()->getId() === $this->getChecker()->getId()) {
                    $context->buildViolation('Backup Checker must be difference with checker')
                        ->atPath('backupChecker')
                        ->addViolation();
                }
            }
        }
    }


}