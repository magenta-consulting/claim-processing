<?php
namespace AppBundle\Admin;

use AppBundle\Entity\CostCentre;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;

class CostCentreAdmin extends BaseAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('code', 'text',['label'=>'Cost Centre Code']);
        $formMapper->add('description', 'textarea',['label'=>'Cost Centre Description']);
        $formMapper->add('enabled', 'checkbox', ['required' => false]);
        $formMapper->add('isDefault', 'checkbox', ['required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('code',null,['label'=>'Cost Centre Code'])
            ->add('enabled');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('code',null,['label'=>'Cost Centre Code'])
            ->add('description',null,['label'=>'Cost Centre Description'])
            ->add('enabled', null, array('editable' => true))
            ->add('isDefault', null, array('editable' => true))
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof CostCentre
            ? $object->getCode()
            : 'Cost Centre Management'; // shown in the breadcrumb on the create view
    }


}