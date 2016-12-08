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
 * @ORM\Table(name="checker_history")
 */
class CheckerHistory
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var claim
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Claim",inversedBy="checkingHistories")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $claim;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position",inversedBy="checkingHistories")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $position;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $checkerPosition;

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
     * @var string
     * @ORM\Column(name="status",type="string")
     */
    private $status;

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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
    public function getCheckerPosition()
    {
        return $this->checkerPosition;
    }

    /**
     * @param Position $checkerPosition
     */
    public function setCheckerPosition($checkerPosition)
    {
        $this->checkerPosition = $checkerPosition;
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