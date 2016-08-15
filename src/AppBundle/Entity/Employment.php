<?php
/**
 * Created by PhpStorm.
 * User: chuyennguyen
 * Date: 8/12/16
 * Time: 6:19 PM
 */

namespace AppBundle\Entity;


class Employment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer",options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    protected $code;

    /**
     * @var integer
     * @ORM\Column(type="string")
     */
    protected $description;
}