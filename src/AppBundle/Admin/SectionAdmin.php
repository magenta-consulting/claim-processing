<?php
namespace AppBundle\Admin;

use AppBundle\Entity\CostCentre;
use AppBundle\Entity\Department;
use AppBundle\Entity\Section;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\ORM\Query\Expr;

class SectionAdmin extends BaseAdmin
{


    protected $parentAssociationMapping = 'department';

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('code', 'text',['label'=>'Section Code']);
        $formMapper->add('description', 'textarea',['label'=>'Section Description']);
        $formMapper->add('department', 'sonata_type_model', array(
            'property' => 'code',
            'query'=>$this->filterDepartmentBycompany(),
            'placeholder' => 'Select Department',
            'empty_data'  => null
        ));
        $formMapper->add('enabled', 'checkbox', ['required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('code',null,['label'=>'Section Code'])
            ->add('enabled')
            ->add('department.code');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('code',null,['label'=>'Section Code'])
            ->add('description',null,['label'=>'Section Description'])
            ->add('department.code')
            ->add('enabled', null, array('editable' => true))
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof Section
            ? $object->getCode()
            : 'Section Management'; // shown in the breadcrumb on the create view
    }

}