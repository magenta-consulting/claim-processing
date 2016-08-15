<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity\Core;

use Application\Sonata\MediaBundle\Entity\Media;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="core__user")
 */
class User extends BaseUser
{


    public function __construct()
    {
        parent::__construct();
        // your own logic
        $this->userSetting = new UserSetting();
        $this->isCompletedProfile = false;
        $this->isVerifiedPhone = false;
    }
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var \DateTime
     * @ORM\Column(name="birthday", type="datetime",nullable=true)
     */
    private $birthday;

    /**
     * @var \DateTime
     * @ORM\Column(name="gender", type="string",nullable=true)
     */
    private $gender;

    /** @var string
     * @ORM\Column(name="first_name",type="string",nullable=true)
     */
    private $firstName;

    /** @var string
     * @ORM\Column(name="last_name",type="string",nullable=true)
     */
    private $lastName;

    /** @var string
     * @ORM\Column(name="phone",type="integer",nullable=true)
     */
    private $phone;

    /** @var string
     * @ORM\Column(name="about_user",type="text",nullable=true)
     */
    private $aboutUser;

    /**
     * @var City
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Core\City")
     */
    private $city;

    /**
     * @var State
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Core\State")
     */
    private $state;

    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media",cascade={"persist"})
     */
    private $photo;

    /**
     * @var UserSetting
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Core\UserSetting",cascade={"persist","remove"})
     */
    private $userSetting;

    /**
     * @var boolean
     * @ORM\Column(name="is_completed_profile",type="boolean",options={"default":false})
     */
    private $isCompletedProfile;
    /**
     * @var boolean
     * @ORM\Column(name="is_verified_phone",type="boolean",options={"default":false})
     */
    private $isVerifiedPhone;

    /**
     * @var integer
     * @ORM\Column(name="verified_code_phone",type="integer",nullable=true)
     */
    private $verifiedCodePhone;

    /**
     * @return int
     */
    public function getVerifiedCodePhone()
    {
        return $this->verifiedCodePhone;
    }

    /**
     * @param int $verifiedCodePhone
     */
    public function setVerifiedCodePhone($verifiedCodePhone)
    {
        $this->verifiedCodePhone = $verifiedCodePhone;
    }
    

    /**
     * @return boolean
     */
    public function isIsCompletedProfile()
    {
        return $this->isCompletedProfile;
    }

    /**
     * @param boolean $isCompletedProfile
     */
    public function setIsCompletedProfile($isCompletedProfile)
    {
        $this->isCompletedProfile = $isCompletedProfile;
    }

    /**
     * @return boolean
     */
    public function isIsVerifiedPhone()
    {
        return $this->isVerifiedPhone;
    }

    /**
     * @param boolean $isVerifiedPhone
     */
    public function setIsVerifiedPhone($isVerifiedPhone)
    {
        $this->isVerifiedPhone = $isVerifiedPhone;
    }

    
    /**
     * @return UserSetting
     */
    public function getUserSetting()
    {
        return $this->userSetting;
    }

    /**
     * @param UserSetting $userSetting
     */
    public function setUserSetting($userSetting)
    {
        $this->userSetting = $userSetting;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * @return \DateTime
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param \DateTime $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
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
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getAboutUser()
    {
        return $this->aboutUser;
    }

    /**
     * @param string $aboutUser
     */
    public function setAboutUser($aboutUser)
    {
        $this->aboutUser = $aboutUser;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return State
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param State $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return Media
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param Media $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }





}