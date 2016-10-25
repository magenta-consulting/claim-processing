<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="company")
 */
class Company
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $address;

    /**
     * @ORM\Column(type="integer")
     */
    protected $bizCode;

    /**
     * @ORM\Column(type="string")
     */
    protected $country;

    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company",inversedBy="children")
     */
    private $parent;
    /**
     * @var
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Company",mappedBy="parent",cascade={"remove"})
     */

    private $children;


    /**
     * @var Position
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Position",mappedBy="company")
     */
    private $positions;


    /**
     * @var Category
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\LimitRule",mappedBy="company")
     */
    private $limitRules;

    public function __construct()
    {
        $this->positions = new ArrayCollection();
        $this->limitRules = new ArrayCollection();
    }

    /**
     * @return Category
     */
    public function getLimitRules()
    {
        return $this->limitRules;
    }

    /**
     * @param Category $limitRules
     */
    public function setLimitRules($limitRules)
    {
        $this->limitRules = $limitRules;
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
     * @return Company
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Company $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }



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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getBizCode()
    {
        return $this->bizCode;
    }

    /**
     * @param mixed $bizCode
     */
    public function setBizCode($bizCode)
    {
        $this->bizCode = $bizCode;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }


}
