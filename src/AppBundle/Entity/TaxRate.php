<?php
/**
 * Created by PhpStorm.
 * User: chuyennguyen
 * Date: 8/12/16
 * Time: 6:23 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="tax_rate")
 */
class TaxRate
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
     * @ORM\Column(name="description",type="string")
     */
    private $description;
    /**
     * @var float
     * @ORM\Column(name="rate",type="float")
     */
    private $rate;
    /**
     * @var boolean
     * @ORM\Column(name="is_local_default",type="boolean")
     */
    private $isLocalDefault;
    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company",inversedBy="taxRates")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id",onDelete="CASCADE")
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
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param float $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    /**
     * @return boolean
     */
    public function isIsLocalDefault()
    {
        return $this->isLocalDefault;
    }

    /**
     * @param boolean $isLocalDefault
     */
    public function setIsLocalDefault($isLocalDefault)
    {
        $this->isLocalDefault = $isLocalDefault;
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
            $criteria->where($expr->eq('isLocalDefault',true))
                ->andWhere($expr->neq('id', $this->id));
            if($company->getTaxRates()->count()) {
                $taxRates = $company->getTaxRates()->matching($criteria);
                if (count($taxRates) && $this->isIsLocalDefault()) {
                    $context->buildViolation('Only one value default at one time.')
                        ->atPath('isLocalDefault')
                        ->addViolation();
                }
            }
        }

    }



}