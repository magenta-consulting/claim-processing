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
    public function filterDepartmentBycompany(){
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('department')
            ->from('AppBundle\Entity\Department','department')
            ->where($expr->eq('department.company', ':company'))
            ->andWhere($expr->eq('department.enabled', true))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('code', 'text');
        $formMapper->add('description', 'textarea');
        $formMapper->add('enabled', 'checkbox', ['required' => false]);
        $formMapper->add('department', 'sonata_type_model', array(
            'property' => 'code',
            'query'=>$this->filterDepartmentBycompany(),
            'placeholder' => 'Select Department',
            'empty_data'  => null
        ));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('code')
            ->add('enabled')
            ->add('department.code');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('code')
            ->add('description')
            ->add('enabled', null, array('editable' => true))
            ->add('department.code')
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