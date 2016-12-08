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
 * @ORM\Table(name="approver_history")
 */
class ApproverHistory
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var claim
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Claim",inversedBy="approverHistories")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $claim;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position",inversedBy="approverHistories")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $position;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $approverPosition;

    /**
     * @var Date
     * @ORM\Column(name="period_from",type="date",nullable=true)
     */
    private $periodFrom;

    /**
     * @var Date
     * @ORM\Column(name="period_to",type="date",nullable=true)
     */
    private $periodTo;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return claim
     */
    public function getClaim()
    {
        return $this->claim;
    }

    /**
     * @param claim $claim
     */
    public function setClaim($claim)
    {
        $this->claim = $claim;
    }

    /**
     * @return Position
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param Position $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return Position
     */
    public function getApproverPosition()
    {
        return $this->approverPosition;
    }

    /**
     * @param Position $approverPosition
     */
    public function setApproverPosition($approverPosition)
    {
        $this->approverPosition = $approverPosition;
    }

    /**
     * @return Date
     */
    public function getPeriodFrom()
    {
        return $this->periodFrom;
    }

    /**
     * @param Date $periodFrom
     */
    public function setPeriodFrom($periodFrom)
    {
        $this->periodFrom = $periodFrom;
    }

    /**
     * @return Date
     */
    public function getPeriodTo()
    {
        return $this->periodTo;
    }

    /**
     * @param Date $periodTo
     */
    public function setPeriodTo($periodTo)
    {
        $this->periodTo = $periodTo;
    }







}