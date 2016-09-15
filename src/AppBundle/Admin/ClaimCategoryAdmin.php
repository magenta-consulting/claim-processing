<?php
namespace AppBundle\Admin;

use AppBundle\Entity\ClaimCategory;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ClaimCategoryAdmin extends BaseAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('code', 'text',['label'=>'Category Code']);
        $formMapper->add('description', 'textarea',['label'=>'Category Description']);
        $formMapper->add('externalCode', 'text',['required'=>false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('code',null,['label'=>'Category Code']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('code',null,['label'=>'Category Code'])
            ->add('description',null,['label'=>'Category Description'])
            ->add('externalCode')
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));

    }

    public function toString($object)
    {
        return $object instanceof ClaimCategory
            ? $object->getCode()
            : 'Claim Category Management'; // shown in the breadcrumb on the create view
    }


}