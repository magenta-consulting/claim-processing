<?php
namespace AppBundle\Admin;

use AppBundle\Admin\Transformer\RolesTransformer;
use AppBundle\Entity\Checker;
use AppBundle\Entity\Position;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\Expr;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CheckerAdmin extends BaseAdmin
{


    protected function configureFormFields(FormMapper $formMapper)
    {

        $formMapper
            ->with('Checker Setup', array('class' => 'col-md-6'))
            ->add('companySetupApprover', 'sonata_type_model', array(
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

            ->with('Checker', array('class' => 'col-md-6'))
            ->add('checker', 'sonata_type_model_list', array(
                'required' => true,
                'btn_add'=>false,
            ))
            ->end()
            ->with('Backup Checker', array('class' => 'col-md-6'))
            ->add('backupChecker', 'sonata_type_model_list', array(
                'required' => true,
                'btn_add'=>false,
            ))
            ->end();

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {

        $datagridMapper->add('companySetupChecker', null, array(), 'entity', array(
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
            ->addIdentifier('companySetupChecker.name', null, array(
                'label'=>'Company'
            ))
            ->add('costCentre.code',null,['label'=>'Cost Centre'])
            ->add('region.code',null,['label'=>'Region'])
            ->add('branch.code',null,['label'=>'Branch'])
            ->add('department.code',null,['label'=>'Department'])
            ->add('section.code',null,['label'=>'Section'])
            ->add('checker.firstName',null,['label'=>'Checker'])
            ->add('backupChecker.firstName',null,['label'=>'Backup Checker'])
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof Checker
            ? $object->getChecker()->getFirstName()
            : 'Checker Grouping'; // shown in the breadcrumb on the create view
    }
}