<?php
/**
 * Created by PhpStorm.
 * User: chuyennguyen
 * Date: 8/12/16
 * Time: 6:23 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="currency_exchange_value")
 */
class CurrencyExchangeValue
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float
     * @ORM\Column(name="ex_rate",type="string")
     */
    private $exRate;
    /**
     * @var date
     * @ORM\Column(name="effective_date",type="date")
     */
    private $effectiveDate;

    /**
     * @var CurrencyExchange
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CurrencyExchange")
     */
    private $currencyExchange;
    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $company;

    /**
     * @var User
     * @ORM\Column(name="modifier",type="string")
     */
    private $modifier;

    /**
     * @return User
     */
    public function getModifier()
    {
        return $this->modifier;
    }

    /**
     * @param User $modifier
     */
    public function setModifier($modifier)
    {
        $this->modifier = $modifier;
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return float
     */
    public function getExRate()
    {
        return $this->exRate;
    }

    /**
     * @param float $exRate
     */
    public function setExRate($exRate)
    {
        $this->exRate = $exRate;
    }

    /**
     * @return date
     */
    public function getEffectiveDate()
    {
        return $this->effectiveDate;
    }

    /**
     * @param date $effectiveDate
     */
    public function setEffectiveDate($effectiveDate)
    {
        $this->effectiveDate = $effectiveDate;
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


}