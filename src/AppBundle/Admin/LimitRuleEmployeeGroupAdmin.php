<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Category;
use AppBundle\Entity\Claim;
use AppBundle\Entity\ClaimCategory;
use AppBundle\Entity\CompanyClaimPolicies;
use AppBundle\Entity\EmployeeGroup;
use AppBundle\Entity\LimitRule;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\ORM\Query\Expr;
use AppBundle\Admin\BaseAdmin;

class LimitRuleEmployeeGroupAdmin extends BaseAdmin
{


    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('claimLimit', 'money', array(
                'label' => 'Claim Limit($)',
                'required' => false,
                'currency'=>'USD'
            ))
            ->add('limitPerYear', null, array(
                'label' => 'Limit Per Year',
                'required' => false
            ))
            ->add('employeeGroup', 'sonata_type_model_list', array(
                'required' => true,
                'btn_add'=>false,
            ));
    }





}