<?php
/**
 * Created by PhpStorm.
 * User: chuyennguyen
 * Date: 8/12/16
 * Time: 6:23 PM
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     * @var boolean
     * @ORM\Column(name="is_default",type="boolean")
     */
    private $isDefault;

    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $company;

    /**
     * @var Company
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\CurrencyExchangeValue",mappedBy="currencyExchange",cascade={"remove"})
     */
    private $currencyExchangeValues;

    public function __construct()
    {
        $this->currencyExchangeValues = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function isIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * @param boolean $isDefault
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    }


    /**
     * @return Company
     */
    public function getCurrencyExchangeValues()
    {
        return $this->currencyExchangeValues;
    }

    /**
     * @param Company $currencyExchangeValues
     */
    public function setCurrencyExchangeValues($currencyExchangeValues)
    {
        $this->currencyExchangeValues = $currencyExchangeValues;
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

    public function validate(ExecutionContextInterface $context, $payload)
    {
        $company = $this->getCompany();
        if($company) {
            $expr = Criteria::expr();
            $criteria = Criteria::create();
            $criteria->where($expr->eq('isDefault',true))
                ->andWhere($expr->neq('id', $this->id));
            if($company->getCurrencyExchanges()->count()) {
                $claimTypes = $company->getCurrencyExchanges()->matching($criteria);
                if (count($claimTypes) && $this->isIsDefault()) {
                    $context->buildViolation('Only one value default at one time.')
                        ->atPath('isDefault')
                        ->addViolation();
                }
            }
        }

    }



}