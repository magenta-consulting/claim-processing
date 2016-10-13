<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="claim_media")
 */
class ClaimMedia
{


    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Claim
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Claim",inversedBy="claimMedias")
     */
    private $claim;

    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media",cascade={"remove","persist"})
     */
    private $media;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Claim
     */
    public function getClaim()
    {
        return $this->claim;
    }

    /**
     * @param Claim $claim
     */
    public function setClaim($claim)
    {
        $this->claim = $claim;
    }

    /**
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param Media $media
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }




















}