<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Core\User;
use AppBundle\Entity\Space\Space;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('email', 'text');
        $formMapper->add('firstName', 'text');
        $formMapper->add('lastName', 'text');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('email')
            ->add('enabled');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('email', null, array(
                'sortable' => 'email',
            ))
            ->add('firstName')
            ->add('lastName')
        ->add('enabled', null, array('editable' => true));
    }
    public function toString($object)
    {
        return $object instanceof User
            ? $object->getName()
            : 'User'; // shown in the breadcrumb on the create view
    }
}