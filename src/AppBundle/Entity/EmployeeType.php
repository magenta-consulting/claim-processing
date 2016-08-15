<?php
/**
 * Created by PhpStorm.
 * User: chuyennguyen
 * Date: 8/12/16
 * Time: 6:19 PM
 */

namespace AppBundle\Entity;


class EmployeeType
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
    private $code;

    /**
     * @var integer
     * @ORM\Column(type="string")
     */
    private $description;

    private $company;
}