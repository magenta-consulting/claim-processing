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
 * @ORM\Table(name="currency_exchange")
 */
class CurrencyExchange
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(name="code",type="string")
     */
    private $code;
    /**
     * @var string
     * @ORM\Column(name="description",type="text")
     */
    private $description;

    /**
     * @var float
     * @ORM\Column(name="ex_rate",type="float")
     */
    private $exRate;
    /**
     * @var date
     * @ORM\Column(name="effective_date",type="date")
     */
    private $effectiveDate;

    /**
     * @var Company
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
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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