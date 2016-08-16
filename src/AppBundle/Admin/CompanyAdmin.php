<?php
namespace AppBundle\Admin;

use AppBundle\Entity\CostCentre;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;

class CompanyAdmin extends AbstractAdmin
{

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    public function getContainer()
    {
        return $this->container;
    }

    public function getCompany(){
        return $this->getContainer()->get('security.token_storage')->getToken()->getUser()->getCompany();
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', 'text');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name');
    }
    public function toString($object)
    {
        return $object instanceof CostCentre
            ? $object->getCode()
            : 'Company Management'; // shown in the breadcrumb on the create view
    }
    public function prePersist($object)
    {
        $object->setCompany($this->getCompany());
    }
//    protected function configureRoutes(RouteCollection $collection)
//    {
//        // prevent display of "Add new" when embedding this form
//            $collection->remove('create');
//    }

}