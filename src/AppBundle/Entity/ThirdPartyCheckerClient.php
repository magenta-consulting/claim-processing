<?php
/**
 * Created by PhpStorm.
 * User: chuyennguyen
 * Date: 8/12/16
 * Time: 6:23 PM
 */

namespace AppBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Entity
 * @ORM\Table(name="third_party_checker_client")
 */
class ThirdPartyCheckerClient
{


    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var ThirdPartyChecker
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ThirdPartyChecker")
     */
    private $thirdPartyChecker;

    /**
     * @var Company
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Company")
     */
    private $client;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ThirdPartyChecker
     */
    public function getThirdPartyChecker()
    {
        return $this->thirdPartyChecker;
    }

    /**
     * @param ThirdPartyChecker $thirdPartyChecker
     */
    public function setThirdPartyChecker($thirdPartyChecker)
    {
        $this->thirdPartyChecker = $thirdPartyChecker;
    }

    /**
     * @return Company
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param Company $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

















}