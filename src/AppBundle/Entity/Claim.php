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
     * @ORM\Column(name="amount",type="float", precision=4, scale=2)
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
     * @var \DateTime
     * @ORM\Column(name="created_at",type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

}