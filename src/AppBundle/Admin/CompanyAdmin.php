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
        return $object instanceof Company
            ? $object->getName()
            : 'Company Management'; // shown in the breadcrumb on the create view
    }

}