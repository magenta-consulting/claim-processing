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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClaimType")
     */
    private $claimType;
    /**
     * @var ClaimCategory
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ClaimCategory")
     */
    private $claimCategory;

    /**
     * @var date
     * @ORM\Column(name="receipt_date",type="date")
     */
    private $receiptDate;
    /**
     * @var CurrencyExchange
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CurrencyExchange")
     */
    private $currencyExchange;
    /**
     * @var float
     * @ORM\Column(name="amount",type="float")
     */
    private $amount;
    /**
     * @var string
     * @ORM\Column(name="purpose_expenses",type="text")
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
     * @var PayCode
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PayCode")
     */
    private $payCode;

    /**
     * @var TaxRate
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TaxRate")
     */
    private $taxRate;

    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     */
    private $company;
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
     * @return TaxRate
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * @param TaxRate $taxRate
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;
    }
    
    

    /**
     * @return PayCode
     */
    public function getPayCode()
    {
        return $this->payCode;
    }

    /**
     * @param PayCode $payCode
     */
    public function setPayCode($payCode)
    {
        $this->payCode = $payCode;
    }



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
    public function getClaimType()
    {
        return $this->claimType;
    }

    /**
     * @param ClaimType $claimType
     */
    public function setClaimType($claimType)
    {
        $this->claimType = $claimType;
    }

    /**
     * @return ClaimCategory
     */
    public function getClaimCategory()
    {
        return $this->claimCategory;
    }

    /**
     * @param ClaimCategory $claimCategory
     */
    public function setClaimCategory($claimCategory)
    {
        $this->claimCategory = $claimCategory;
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