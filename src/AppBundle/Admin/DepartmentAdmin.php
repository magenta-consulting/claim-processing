<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Department;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Admin\AdminInterface;

class DepartmentAdmin extends BaseAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('code', 'text',['label'=>'Department Code']);
        $formMapper->add('description', 'textarea',['label'=>'Department Description']);
        $formMapper->add('enabled', 'checkbox', ['required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('code',null,['label'=>'Department Code'])
            ->add('enabled');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('code',null,['label'=>'Department Code'])
            ->add('description',null,['label'=>'Department Description'])
            ->add('enabled', null, array('editable' => true))
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
//                    'View' => array(
//                        'template' => 'AppBundle:SonataAdmin/CustomActions:_list-action-department.html.twig'
//                    )
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof Department
            ? $object->getCode()
            : 'Department Management'; // shown in the breadcrumb on the create view
    }

}