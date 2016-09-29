<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Category;
use AppBundle\Entity\Claim;
use AppBundle\Entity\ClaimCategory;
use AppBundle\Entity\CompanyClaimPolicies;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\ORM\Query\Expr;
use AppBundle\Admin\BaseAdmin;

class CategoryAdmin extends BaseAdmin
{


    protected function configureFormFields(FormMapper $formMapper)
    {


        $formMapper
            ->with('Group A', array('class' => 'col-md-6'))
            ->add('companyGetRule', 'sonata_type_model', array(
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
            ->with('Group B', array('class' => 'col-md-6'))
            ->add('employeeType', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterEmployeeTypeBycompany(),
                'placeholder' => 'Select Employee Type',
                'empty_data' => null,
                'required' => false,
                'btn_add' => false
            ))
            ->add('payCode', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterPayCostBycompany(),
                'placeholder' => 'Select Pay Code',
                'empty_data' => null,
                'required' => false,
                'btn_add' => false
            ))
            ->add('taxRate', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterTaxRateBycompany(),
                'placeholder' => 'Select Tax Rate',
                'empty_data' => null,
                'required' => false,
                'btn_add' => false
            ))
            ->add('claimType', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterClaimTypeBycompany(),
                'placeholder' => 'Select Type',
                'empty_data' => null,
                'required' => false,
                'btn_add' => false
            ))
            ->add('claimCategory', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterClaimCategoryBycompany(),
                'placeholder' => 'Select Category',
                'empty_data' => null,
                'required' => false,
                'btn_add' => false
            ))
            ->add('claimLimit', 'number', array(
                'label' => 'Claim Limit($)',
                'required' => false
            ))
            ->add('limitPerYear', null, array(
                'label' => 'Limit Per Year',
                'required' => false
            ))
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('claimType.code');
        $datagridMapper->add('claimCategory.code', null, ['label' => 'Category Code']);
        $datagridMapper->add('taxRate.code', null, ['label' => 'Tax Code']);
        $datagridMapper->add('payCode.code', null, ['label' => 'Pay Code']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('claimType.code')
            ->add('claimType.claimTypeType.name', null, ['label' => 'Claim Type'])
            ->add('claimCategory.code', null, ['label' => 'Category Code'])
            ->add('claimCategory.description', null, ['label' => 'Category Description'])
            ->add('taxRate.code', null, ['label' => 'Tax Code'])
            ->add('payCode.code', null, ['label' => 'Pay Code'])
            ->add('claimLimitDescription', null, ['label' => 'Limit Description'])
            ->add('claimLimit', null, ['label' => 'Claim Limit($)'])
            ->add('hasClaimLimit', null, ['label' => 'Has Claim Limit'])
            ->add('limitPerYear', null)
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function buildRule(Category $category)
    {
        $listRule = [];
        $listRule[] = $category->getCompanyGetRule()->getName();
        $listRule[] = $category->getCostCentre()->getCode();
        if ($category->getRegion()) {
            $listRule[] = $category->getRegion()->getCode();
        }
        if ($category->getBranch()) {
            $listRule[] = $category->getBranch()->getCode();
        }
        if ($category->getDepartment()) {
            $listRule[] = $category->getDepartment()->getCode();
        }
        if ($category->getSection()) {
            $listRule[] = $category->getSection()->getCode();
        }
        if ($category->getEmployeeType()) {
            $listRule[] = $category->getEmployeeType()->getCode();
        }
        if ($category->getClaimType()) {
            $listRule[] = $category->getClaimType()->getCode();
        }
        if ($category->getClaimCategory()) {
            $listRule[] = $category->getClaimCategory()->getCode();
        }

        $listRuleStr = implode('>', $listRule);
        return $listRuleStr;
    }

    public function prePersist($object)
    {
        $object->setClaimLimitDescription($this->buildRule($object));
        if ($object->getClaimLimit()) {
            $object->setHasClaimLimit(true);
        } else {
            $object->setHasClaimLimit(false);
        }
        parent::prePersist($object); // TODO: Change the autogenerated stub
    }

    public function preUpdate($object)
    {
        $object->setClaimLimitDescription($this->buildRule($object));
        if ($object->getClaimLimit()) {
            $object->setHasClaimLimit(true);
        } else {
            $object->setHasClaimLimit(false);
        }
        parent::preUpdate($object); // TODO: Change the autogenerated stub
    }

    public function toString($object)
    {
        return $object instanceof Category
            ? $object->getClaimLimitDescription()
            : 'Claims Limit Rules'; // shown in the breadcrumb on the create view
    }


}