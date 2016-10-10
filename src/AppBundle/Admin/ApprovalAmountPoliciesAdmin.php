<?php
namespace AppBundle\Admin;

use AppBundle\Entity\ApprovalAmountPolicies;
use AppBundle\Entity\CostCentre;
use AppBundle\Entity\Department;
use AppBundle\Entity\PayCode;
use AppBundle\Entity\Section;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\ORM\Query\Expr;

class ApprovalAmountPoliciesAdmin extends BaseAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Approver Setup')
            ->with('Approver Setup ', array('class' => 'col-md-6'))
            ->add('companySetupApproval', 'sonata_type_model', array(
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
                'btn_add' => false
            ))
            ->add('region', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterRegionBycompany(),
                'placeholder' => 'Select Region',
                'empty_data' => null,
                'required' => false,
                'btn_add' => false
            ))
            ->add('branch', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterBranchBycompany(),
                'placeholder' => 'Select Branch',
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
            ->add('section', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterSectionBycompany(),
                'placeholder' => 'Select Section',
                'empty_data' => null,
                'required' => false,
                'btn_add' => false
            ))
            ->end()
            ->with('Approver Setup', array('class' => 'col-md-6'))
            ->add('employeeType', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterEmployeeTypeBycompany(),
                'placeholder' => 'Select Employee Type',
                'empty_data' => null,
                'required' => false,
                'btn_add' => false
            ))
            ->end()
            ->end()
            ->tab('Approver')
            ->with('Approver 1', array('class' => 'col-md-12'))
            ->add('approver1', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label'=>'Approver 1'
            ))
            ->add('backupApprover1', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label'=>'Backup Approver 1'
            ))
            ->add('overrideApprover1', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label'=>'Override Approver 1'
            ))
            ->add('approval1Amount', 'money', [
                'currency' => 'USD',
                'required' => false,
                'label'=>'Approver 1 Amount Max'
            ])
            ->add('approval1AmountStatus', 'checkbox', [
                'required' => false,
                'label'=>'Active'
            ])
            ->end()

            ->with('Approver 2', array('class' => 'col-md-12'))
            ->add('approver2', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label'=>'Approver 2'
            ))
            ->add('backupApprover2', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label'=>'Backup Approver 2'
            ))
            ->add('overrideApprover2', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label'=>'Override Approver 2'
            ))
            ->add('approval2Amount', 'money', [
                'currency' => 'USD',
                'required' => false,
                'label'=>'Approver 2 Amount Max'
            ])
            ->add('approval2AmountStatus', 'checkbox', [
                'required' => false,
                'label'=>'Active'
            ])
            ->end()

            ->with('Approver 3', array('class' => 'col-md-12'))
            ->add('approver3', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label'=>'Approver 3'
            ))
            ->add('backupApprover3', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label'=>'Backup Approver 3'
            ))
            ->add('overrideApprover3', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label'=>'Override Approver 3'
            ))
            ->add('approval3Amount', 'money', [
                'currency' => 'USD',
                'required' => false,
                'label'=>'Approver 3 Amount Max'
            ])
            ->add('approval3AmountStatus', 'checkbox', [
                'required' => false,
                'label'=>'Active'
            ])
            ->end()

            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('companySetupApproval', null, array(), 'entity', array(
            'class' => 'AppBundle\Entity\Company',
            'choice_label' => 'name',
            'query_builder' => $this->filterCompanyBycompany(),
        ));
        $datagridMapper->add('costCentre', null, array(), 'entity', array(
            'class' => 'AppBundle\Entity\CostCentre',
            'choice_label' => 'code',
            'query_builder' => $this->filterCostCentreBycompany(),
        ));
        $datagridMapper->add('region', null, array(), 'entity', array(
            'class' => 'AppBundle\Entity\Region',
            'choice_label' => 'code',
            'query_builder' => $this->filterRegionBycompany(),
        ));
        $datagridMapper->add('branch', null, array(), 'entity', array(
            'class' => 'AppBundle\Entity\Branch',
            'choice_label' => 'code',
            'query_builder' => $this->filterBranchBycompany(),
        ));
        $datagridMapper->add('department', null, array(), 'entity', array(
            'class' => 'AppBundle\Entity\Department',
            'choice_label' => 'code',
            'query_builder' => $this->filterDepartmentBycompany(),
        ));
        $datagridMapper->add('section', null, array(), 'entity', array(
            'class' => 'AppBundle\Entity\Section',
            'choice_label' => 'code',
            'query_builder' => $this->filterSectionBycompany(),
        ));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('companySetupApproval.name', null, array(
                'label' => 'Company'
            ))
            ->add('costCentre.code', null, ['label' => 'Cost Centre'])
            ->add('region.code', null, ['label' => 'Region'])
            ->add('branch.code', null, ['label' => 'Branch'])
            ->add('department.code', null, ['label' => 'Department'])
            ->add('section.code', null, ['label' => 'Section'])
            ->add('employeeType.code', null, ['label' => 'Employee Type'])
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof ApprovalAmountPolicies
            ? $object->getCompanySetupApproval()->getName()
            : 'Approval Amount Policies Management'; // shown in the breadcrumb on the create view
    }

}