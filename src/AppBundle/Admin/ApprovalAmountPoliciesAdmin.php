<?php
namespace AppBundle\Admin;

use AppBundle\Entity\ApprovalAmountPolicies;
use AppBundle\Entity\CostCentre;
use AppBundle\Entity\Department;
use AppBundle\Entity\PayCode;
use AppBundle\Entity\Section;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\ORM\Query\Expr;

class ApprovalAmountPoliciesAdmin extends BaseAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('claimType', 'sonata_type_model', array(
            'property' => 'code',
            'query'=>$this->filterClaimTypeBycompany(),
            'placeholder' => 'Select Claim Type',
            'empty_data'  => null
        ));
        $formMapper->add('costCentre', 'sonata_type_model', array(
            'property' => 'code',
            'query'=>$this->filterCostCentreBycompany(),
            'placeholder' => 'Select Claim Type',
            'empty_data'  => null
        ));
        $formMapper->add('approval1Max', 'number',['required'=>false]);
        $formMapper->add('approval2Max', 'number',['required'=>false]);
        $formMapper->add('approval3Max', 'number',['required'=>false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('claimType.code')
            ->add('costCentre.code');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('claimType.code')
            ->add('costCentre.code')
            ->add('approval1Max')
            ->add('approval2Max')
            ->add('approval3Max')
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof ApprovalAmountPolicies
            ? $object->getClaimType()->getCode()
            : 'Approval Amount Policies Management'; // shown in the breadcrumb on the create view
    }

}