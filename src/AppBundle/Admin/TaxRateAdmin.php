<?php
namespace AppBundle\Admin;

use AppBundle\Entity\CostCentre;
use AppBundle\Entity\Region;
use AppBundle\Entity\TaxRate;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;

class TaxRateAdmin extends BaseAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('code', 'text',['label'=>'Tax Code']);
        $formMapper->add('description', 'textarea',['label'=>'Tax Desciption']);
        $formMapper->add('rate', 'number',['label'=>'Rate (%)']);
        $formMapper->add('isLocalDefault', 'checkbox', ['required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('code',null,['label'=>'Tax Code'])
            ->add('isLocalDefault');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('code',null,['label'=>'Tax Code'])
            ->add('description',null,['label'=>'Tax Description'])
            ->add('rate')
            ->add('isLocalDefault', null, array('editable' => true))
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof TaxRate
            ? $object->getCode()
            : 'Tax Rate Management'; // shown in the breadcrumb on the create view
    }



}