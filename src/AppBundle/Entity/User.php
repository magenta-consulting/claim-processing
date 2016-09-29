<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser implements EquatableInterface
{



    public function isEqualTo(UserInterface $user)
    {
        if ($user instanceof User) {
            // Check that the roles are the same, in any order
            $isEqual = count($this->getRoles()) == count($user->getRoles());
            if ($isEqual) {
                foreach($this->getRoles() as $role) {
                    $isEqual = $isEqual && in_array($role, $user->getRoles());
                }
            }
            return $isEqual;
        }

        return false;
    }
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Position
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Position", mappedBy="user")
     */
    private $positions;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     */
    private $company;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Position
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * @param Position $positions
     */
    public function setPositions($positions)
    {
        $this->positions = $positions;
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