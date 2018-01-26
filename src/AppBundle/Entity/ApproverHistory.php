<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="approver_history")
 */
class ApproverHistory {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	
	function __construct() {
		$this->createdAt = new \DateTime();
	}
	
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
	 * @var \DateTime
	 * @ORM\Column(name="created_at", type="datetime",nullable=true)
	 */
	private $createdAt;
	
	/**
	 * @var \DateTime
	 * @ORM\Column(name="period_from",type="date",nullable=true)
	 */
	private $periodFrom;
	
	/**
	 * @var \DateTime
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
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}
	
	/**
	 * @param string $status
	 */
	public function setStatus($status) {
		$this->status = $status;
	}
	
	
	/**
	 * @return claim
	 */
	public function getClaim() {
		return $this->claim;
	}
	
	/**
	 * @param claim $claim
	 */
	public function setClaim($claim) {
		$this->claim = $claim;
	}
	
	/**
	 * @return Position
	 */
	public function getPosition() {
		return $this->position;
	}
	
	/**
	 * @param Position $position
	 */
	public function setPosition($position) {
		$this->position = $position;
	}
	
	/**
	 * @return Position
	 */
	public function getApproverPosition() {
		return $this->approverPosition;
	}
	
	/**
	 * @param Position $approverPosition
	 */
	public function setApproverPosition($approverPosition) {
		$this->approverPosition = $approverPosition;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getPeriodFrom() {
		return $this->periodFrom;
	}
	
	/**
	 * @param \DateTime $periodFrom
	 */
	public function setPeriodFrom($periodFrom) {
		$this->periodFrom = $periodFrom;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getPeriodTo() {
		return $this->periodTo;
	}
	
	/**
	 * @param \DateTime $periodTo
	 */
	public function setPeriodTo($periodTo) {
		$this->periodTo = $periodTo;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getCreatedAt(){
		return $this->createdAt;
	}
	
	/**
	 * @param \DateTime $createdAt
	 */
	public function setCreatedAt(\DateTime $createdAt){
		$this->createdAt = $createdAt;
	}
	
}