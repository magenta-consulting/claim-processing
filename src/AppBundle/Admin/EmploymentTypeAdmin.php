<?php
namespace AppBundle\Admin;

use AppBundle\Entity\CostCentre;
use AppBundle\Entity\Department;
use AppBundle\Entity\EmployeeType;
use AppBundle\Entity\PayCode;
use AppBundle\Entity\Section;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\ORM\Query\Expr;

class EmploymentTypeAdmin extends BaseAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('code', 'text');
        $formMapper->add('description', 'textarea');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('code');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('code')
            ->add('description')
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof EmployeeType
            ? $object->getCode()
            : 'Employee Type Management'; // shown in the breadcrumb on the create view
    }

}