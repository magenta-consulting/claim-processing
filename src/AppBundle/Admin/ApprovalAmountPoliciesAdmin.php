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
            ->with('Employee Groups', array('class' => 'col-md-12'))
            ->add('approvalAmountPoliciesEmployeeGroups', 'sonata_type_collection', array(
                'label' => 'Employee Groups',
                'required' => false,
            ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                ))
            ->end()
            ->with('Approver 1', array('class' => 'col-md-12'))
            ->add('approver1', 'sonata_type_model_list', array(
                'required' => true,
                'btn_add' => false,
                'label' => 'Approver 1'
            ))
            ->add('backupApprover1', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label' => 'Backup Approver 1'
            ))
            ->add('overrideApprover1', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label' => 'Override Approver 1'
            ))
            ->add('approval1Amount', 'money', [
                'currency' => 'USD',
                'required' => false,
                'label' => 'Approver 1 Amount Max'
            ])
            ->add('approval1AmountStatus', 'checkbox', [
                'required' => false,
                'label' => 'Active'
            ])
            ->end()
            ->with('Approver 2', array('class' => 'col-md-12'))
            ->add('approver2', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label' => 'Approver 2'
            ))
            ->add('backupApprover2', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label' => 'Backup Approver 2'
            ))
            ->add('overrideApprover2', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label' => 'Override Approver 2'
            ))
            ->add('approval2Amount', 'money', [
                'currency' => 'USD',
                'required' => false,
                'label' => 'Approver 2 Amount Max'
            ])
            ->add('approval2AmountStatus', 'checkbox', [
                'required' => false,
                'label' => 'Active'
            ])
            ->end()
            ->with('Approver 3', array('class' => 'col-md-12'))
            ->add('approver3', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label' => 'Approver 3'
            ))
            ->add('backupApprover3', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label' => 'Backup Approver 3'
            ))
            ->add('overrideApprover3', 'sonata_type_model_list', array(
                'required' => false,
                'btn_add' => false,
                'label' => 'Override Approver 3'
            ))
            ->add('approval3Amount', 'money', [
                'currency' => 'USD',
                'required' => false,
                'label' => 'Approver 3 Amount Max'
            ])
            ->add('approval3AmountStatus', 'checkbox', [
                'required' => false,
                'label' => 'Active'
            ])
            ->end();

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {

    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('approver1.firstName', null, ['label' => 'Approver1'])
            ->add('approver2.firstName', null, ['label' => 'Approver2'])
            ->add('approver3.firstName', null, ['label' => 'Approver3'])
            ->add('1', 'approver_employee_groups', ['label' => 'Employee Groups'])
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }
	
	/**
	 * @param ApprovalAmountPolicies $approvalAmountPolicies
	 */
    public function manualUpdate($approvalAmountPolicies)
    {
        foreach ($approvalAmountPolicies->getApprovalAmountPoliciesEmployeeGroups() as $approvalAmountPoliciesEmployeeGroup) {
            $approvalAmountPolicies->addApprovalAmountPoliciesEmployeeGroup($approvalAmountPoliciesEmployeeGroup);
        }
    }

    public function toString($object)
    {
        return $object instanceof ApprovalAmountPolicies
            ? 'Approval Policy'
            : 'Approval Policies Management'; // shown in the breadcrumb on the create view
    }

}