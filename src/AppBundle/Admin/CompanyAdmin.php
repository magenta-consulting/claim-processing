<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Company;
use AppBundle\Entity\CostCentre;
use AppBundle\Entity\Department;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use AppBundle\Admin\BaseAdmin;

class CompanyAdmin extends BaseAdmin
{


    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', 'text');
        $formMapper->add('address', 'text');
        $formMapper->add('bizCode', 'text');
        $formMapper->add('country', 'text');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('address')
            ->add('bizCode')
            ->add('country')
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                    'View' => array(
                        'template' => 'AppBundle:SonataAdmin/CustomActions:_list-action-company.html.twig'
                    )


                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof Company
            ? $object->getName()
            : 'Company Management'; // shown in the breadcrumb on the create view
    }


    protected function configureRoutes(RouteCollection $collection)
    {

        if($this->isHr()){
            $collection->remove('delete');
            $collection->remove('list');
            $collection->remove('create');
        }
    }

}