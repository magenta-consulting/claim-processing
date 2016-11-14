<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Category;
use AppBundle\Entity\Claim;
use AppBundle\Entity\ClaimCategory;
use AppBundle\Entity\CompanyClaimPolicies;
use AppBundle\Entity\EmployeeGroup;
use AppBundle\Entity\LimitRule;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\ORM\Query\Expr;
use AppBundle\Admin\BaseAdmin;

class EmployeeGroupAdmin extends BaseAdmin
{


    protected function configureFormFields(FormMapper $formMapper)
    {


        $formMapper
            ->add('companyApply', 'sonata_type_model', array(
                'property' => 'name',
                'query' => $this->filterCompanyBycompany(),
                'placeholder' => 'Select Company',
                'empty_data' => null,
                'label' => 'Company',
                'btn_add' => false
            ))
            ->add('costCentre', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterCostCentreBycompany(),
                'placeholder' => 'Select Cost Centre',
                'empty_data' => null,
                'required' => false,
                'btn_add' => false
            ))
            ->add('department', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterDepartmentBycompany(),
                'placeholder' => 'Select Department',
                'empty_data' => null,
                'required' => false,
                'btn_add' => false
            ))
            ->add('employeeType', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterEmployeeTypeBycompany(),
                'placeholder' => 'Select Employee Type',
                'empty_data' => null,
                'required' => false,
                'btn_add' => false
            ));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('companyApply.name',null,['label'=>'Company']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('companyApply.name',null,['label'=>'Company'])
            ->add('costCentre.code',null,['label'=>'Cost Centre'])
            ->add('department.code', null,['label'=>'Department'])
            ->add('employeeType.code', null, ['label'=>'Employee Type'])
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }
    public function manualUpdate($employeeGroup)
    {
        $description = $this->getContainer()->get('app.claim_rule')->getDescriptionEmployeeGroup($employeeGroup);
        $employeeGroup->setDescription($description);
    }

    public function toString($object)
    {
        return $object instanceof EmployeeGroup
            ? $object->getDescription()
            : 'Employee Group Management'; // shown in the breadcrumb on the create view
    }




}