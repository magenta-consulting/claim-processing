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
        parent::__construct();
        // your own logic
    }
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
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

    private $image;
    private $company;




}