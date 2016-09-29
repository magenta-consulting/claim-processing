<?php
namespace AppBundle\Admin;

use AppBundle\Entity\ClaimType;
use AppBundle\Entity\CostCentre;
use AppBundle\Entity\Region;
use AppBundle\Entity\TaxRate;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\ORM\Query\Expr;

class ClaimTypeAdmin extends BaseAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('code', 'text',['label'=>'Claim Type Code']);
        $formMapper->add('claimTypeType', 'sonata_type_model', array(
            'property' => 'name',
            'query'=>$this->filterClaimTypeTypeBycompany(),
            'placeholder' => 'Select Type',
            'empty_data'  => null,
            'label'=>'Claim Type'
        ));
        $formMapper->add('enabled', 'checkbox', ['required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('code',null,['label'=>'Claim Type Code'])
            ->add('enabled');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('code',null,['label'=>'Claim Type Code'])
            ->add('claimTypeType.name',null,['label'=>'Claim Type'])
            ->add('enabled', null, array('editable' => true))
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof ClaimType
            ? $object->getCode()
            : 'Claim Type Management'; // shown in the breadcrumb on the create view
    }



}