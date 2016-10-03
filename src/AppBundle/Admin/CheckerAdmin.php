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
            ->with('Checker Setup', array('class' => 'col-md-12'))
            ->add('companySetupChecker', 'sonata_type_model', array(
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

            ->with('Checker', array('class' => 'col-md-12'))
            ->add('checker', 'sonata_type_model_autocomplete', array(
                'property' => 'email',
                'multiple' => false,
                'required' => true,
            ))
            ->end()
            ->with('Backup Checker', array('class' => 'col-md-12'))
            ->add('backupChecker', 'sonata_type_model_autocomplete', array(
                'property' => 'email',
                'multiple' => false,
                'required' => true,
            ))
            ->end();

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('companySetupChecker.name', null, ['label' => 'Company']);
        $datagridMapper->add('costCentre.code', null, ['label' => 'Cost Centre']);
        $datagridMapper->add('region.code', null, ['label' => 'Region']);
        $datagridMapper->add('branch.code', null, ['label' => 'Branch']);
        $datagridMapper->add('department.code', null, ['label' => 'Department']);
        $datagridMapper->add('section.code', null, ['label' => 'Section']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('companySetupChecker.name', null, array(
            ))
            ->add('costCentre.code')
            ->add('region.code')
            ->add('branch.code')
            ->add('department.code')
            ->add('checker.firstName')
            ->add('backupChecker.firstName')
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