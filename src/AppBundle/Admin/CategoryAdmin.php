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


    public function filterCompanyBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('company')
            ->from('AppBundle\Entity\Company', 'company')
            ->where(
                $expr->orX(
                    $expr->eq('company.parent', ':company'),
                    $expr->eq('company', ':company')
                )
            )
            ->setParameter('company', $this->getCompany());
        return $qb;
    }

    public function filterCostCentreBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('costCentre')
            ->from('AppBundle\Entity\CostCentre', 'costCentre')
            ->where($expr->eq('costCentre.company', ':company'))
            ->andWhere($expr->eq('costCentre.enabled', true))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }

    public function filterRegionBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('region')
            ->from('AppBundle\Entity\Region', 'region')
            ->where($expr->eq('region.company', ':company'))
            ->andWhere($expr->eq('region.enabled', true))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }

    public function filterBranchBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('branch')
            ->from('AppBundle\Entity\Branch', 'branch')
            ->where($expr->eq('branch.company', ':company'))
            ->andWhere($expr->eq('branch.enabled', true))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }

    public function filterDepartmentBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('department')
            ->from('AppBundle\Entity\Department', 'department')
            ->where($expr->eq('department.company', ':company'))
            ->andWhere($expr->eq('department.enabled', true))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }

    public function filterSectionBycompany(){
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('section')
            ->from('AppBundle\Entity\Section','section')
            ->where($expr->eq('section.company', ':company'))
            ->andWhere($expr->eq('section.enabled', true))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }

    public function filterEmployeeTypeBycompany(){
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('employeeType')
            ->from('AppBundle\Entity\EmployeeType','employeeType')
            ->where($expr->eq('employeeType.company', ':company'))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }

    public function filterClaimTypeBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('claimType')
            ->from('AppBundle\Entity\ClaimType', 'claimType')
            ->where($expr->eq('claimType.company', ':company'))
            ->andWhere($expr->eq('claimType.enabled', true))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }

    public function filterClaimCategoryBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('claimCategory')
            ->from('AppBundle\Entity\ClaimCategory', 'claimCategory')
            ->where($expr->eq('claimCategory.company', ':company'))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }

    public function filterTaxRateBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('taxRate')
            ->from('AppBundle\Entity\TaxRate', 'taxRate')
            ->where($expr->eq('taxRate.company', ':company'))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }

    public function filterPayCostBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('payCode')
            ->from('AppBundle\Entity\PayCode', 'payCode')
            ->where($expr->eq('payCode.company', ':company'))
            ->andWhere($expr->eq('payCode.enabled', true))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {


        $formMapper->add('companyGetRule', 'sonata_type_model', array(
            'property' => 'name',
            'query' => $this->filterCompanyBycompany(),
            'placeholder' => 'Select Company',
            'empty_data' => null,
            'label'=>'Company'
        ));
        $formMapper->add('costCentre', 'sonata_type_model', array(
            'property' => 'code',
            'query' => $this->filterCostCentreBycompany(),
            'placeholder' => 'Select Cost Centre',
            'empty_data' => null,
        ));
        $formMapper->add('region', 'sonata_type_model', array(
            'property' => 'code',
            'query' => $this->filterRegionBycompany(),
            'placeholder' => 'Select Region',
            'empty_data' => null,
            'required' => false
        ));
        $formMapper->add('branch', 'sonata_type_model', array(
            'property' => 'code',
            'query' => $this->filterBranchBycompany(),
            'placeholder' => 'Select Branch',
            'empty_data' => null,
            'required' => false
        ));
        $formMapper->add('department', 'sonata_type_model', array(
            'property' => 'code',
            'query' => $this->filterDepartmentBycompany(),
            'placeholder' => 'Select Department',
            'empty_data' => null,
            'required' => false
        ));
        $formMapper->add('section', 'sonata_type_model', array(
            'property' => 'code',
            'query' => $this->filterSectionBycompany(),
            'placeholder' => 'Select Section',
            'empty_data' => null,
            'required' => false
        ));
        $formMapper->add('employeeType', 'sonata_type_model', array(
            'property' => 'code',
            'query' => $this->filterEmployeeTypeBycompany(),
            'placeholder' => 'Select Employee Type',
            'empty_data' => null,
            'required' => false
        ));
        $formMapper->add('payCode', 'sonata_type_model', array(
            'property' => 'code',
            'query' => $this->filterPayCostBycompany(),
            'placeholder' => 'Select Pay Code',
            'empty_data' => null,
            'required' => false
        ));
        $formMapper->add('taxRate', 'sonata_type_model', array(
            'property' => 'code',
            'query' => $this->filterTaxRateBycompany(),
            'placeholder' => 'Select Tax Rate',
            'empty_data' => null,
            'required' => false
        ));
        $formMapper->add('claimType', 'sonata_type_model', array(
            'property' => 'code',
            'query' => $this->filterClaimTypeBycompany(),
            'placeholder' => 'Select Type',
            'empty_data' => null,
            'required' => false
        ));
        $formMapper->add('claimCategory', 'sonata_type_model', array(
            'property' => 'code',
            'query' => $this->filterClaimCategoryBycompany(),
            'placeholder' => 'Select Category',
            'empty_data' => null,
            'required' => false
        ));
        $formMapper->add('claimLimit', 'number', array(
            'label' => 'Claim Limit($)',
            'required' => false
        ));
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
            ->add('taxRate.description', null, ['label' => 'Tax Description'])
            ->add('payCode.code', null, ['label' => 'Pay Code'])
            ->add('claimLimitDescription', null, ['label' => 'Limit Description'])
            ->add('claimLimit', null, ['label' => 'Claim Limit($)'])
            ->add('hasClaimLimit', null, ['label' => 'Has Claim Limit'])
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function buildRule(Category $category){
        $listRule = [];
        $listRule[] = $category->getCompanyGetRule()->getName();
        $listRule[] = $category->getCostCentre()->getCode();
        if($category->getRegion()){
            $listRule[]=$category->getRegion()->getCode();
        }
        if($category->getBranch()){
            $listRule[] = $category->getBranch()->getCode();
        }
        if($category->getDepartment()){
            $listRule[] = $category->getDepartment()->getCode();
        }
        if($category->getSection()){
            $listRule[] =$category->getSection()->getCode();
        }
        if($category->getEmployeeType()){
            $listRule[] = $category->getEmployeeType()->getCode();
        }
        if($category->getClaimType()){
            $listRule[] = $category->getClaimType()->getCode();
        }
        if($category->getClaimCategory()){
            $listRule[]=$category->getClaimCategory()->getCode();
        }

        $listRuleStr = implode('>',$listRule);
        return $listRuleStr;
    }

    public function prePersist($object)
    {
        $object->setClaimLimitDescription($this->buildRule($object));
        if($object->getClaimLimit()){
            $object->setHasClaimLimit(true);
        }else{
            $object->setHasClaimLimit(false);
        }
        parent::prePersist($object); // TODO: Change the autogenerated stub
    }

    public function preUpdate($object)
    {
        $object->setClaimLimitDescription($this->buildRule($object));
        if($object->getClaimLimit()){
            $object->setHasClaimLimit(true);
        }else{
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