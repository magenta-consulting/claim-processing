<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="position_submitter")
 */
class PositionSubmitter
{


    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position",inversedBy="submissionBy")
     * @ORM\JoinColumn(name="proxy_position_id",referencedColumnName="id")
     */
    private $submissionByPosition;

    /**
     * @var Position
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Position",inversedBy="submissionFor")
     * @ORM\JoinColumn(name="position_id",referencedColumnName="id")
     */
    private $submissionForPosition;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Position
     */
    public function getSubmissionByPosition()
    {
        return $this->submissionByPosition;
    }

    /**
     * @param Position $submissionByPosition
     */
    public function setSubmissionByPosition($submissionByPosition)
    {
        $this->submissionByPosition = $submissionByPosition;
    }

    /**
     * @return Position
     */
    public function getSubmissionForPosition()
    {
        return $this->submissionForPosition;
    }

    /**
     * @param Position $submissionForPosition
     */
    public function setSubmissionForPosition($submissionForPosition)
    {
        $this->submissionForPosition = $submissionForPosition;
    }

















}