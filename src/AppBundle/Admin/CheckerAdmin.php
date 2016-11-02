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
            ->with('Employee Groups', array('class' => 'col-md-12'))
            ->add('checkerEmployeeGroups', 'sonata_type_collection', array(
                'label' => 'Employee Groups',
                'required' => false,
            ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                ))
            ->end()
            ->with('Checker', array('class' => 'col-md-12'))
            ->add('checker', 'sonata_type_model_list', array(
                'required' => true,
                'btn_add' => false,
            ))
            ->add('backupChecker', 'sonata_type_model_list', array(
                'btn_add' => false,
            ))
            ->end();


    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {


    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('checker.firstName', null, ['label' => 'Checker'])
            ->add('backupChecker.firstName', null, ['label' => 'Backup Checker'])
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function manualUpdate($checker)
    {
        foreach ($checker->getCheckerEmployeeGroups() as $checkerEmployeeGroup) {
            $checker->addCheckerEmployeeGroup($checkerEmployeeGroup);
        }
    }


    public function toString($object)
    {
        return $object instanceof Checker
            ? $object->getId()
            : 'Checker Grouping'; // shown in the breadcrumb on the create view
    }
}