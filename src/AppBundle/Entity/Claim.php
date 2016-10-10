<?php
/**
 * Created by PhpStorm.
 * User: chuyennguyen
 * Date: 8/12/16
 * Time: 6:23 PM
 */

namespace AppBundle\Entity;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="claim")
 */


class Claim
{


    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var ClaimType
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category")
     */
    private $category;

    /**
     * @var date
     * @ORM\Column(name="receipt_date",type="date",nullable=true)
     */
    private $receiptDate;
    /**
     * @var CurrencyExchange
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CurrencyExchange")
     */
    private $currencyExchange;
    /**
     * @var float
     * @ORM\Column(name="amount",type="float",nullable=true)
     */
    private $amount;
    /**
     * @var string
     * @ORM\Column(name="purpose_expenses",type="text",nullable=true)
     */
    private $purposeExpenses;
    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media")
     */
    private $image;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    private $user;

    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     */
    private $company;




    /**
     * @var \DateTime
     * @ORM\Column(name="created_at",type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return ClaimType
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param ClaimType $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return date
     */
    public function getReceiptDate()
    {
        return $this->receiptDate;
    }

    /**
     * @param date $receiptDate
     */
    public function setReceiptDate($receiptDate)
    {
        $this->receiptDate = $receiptDate;
    }

    /**
     * @return CurrencyExchange
     */
    public function getCurrencyExchange()
    {
        return $this->currencyExchange;
    }

    /**
     * @param CurrencyExchange $currencyExchange
     */
    public function setCurrencyExchange($currencyExchange)
    {
        $this->currencyExchange = $currencyExchange;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getPurposeExpenses()
    {
        return $this->purposeExpenses;
    }

    /**
     * @param string $purposeExpenses
     */
    public function setPurposeExpenses($purposeExpenses)
    {
        $this->purposeExpenses = $purposeExpenses;
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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }





}