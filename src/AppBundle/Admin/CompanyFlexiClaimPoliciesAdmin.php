<?php
namespace AppBundle\Admin;

use AppBundle\Entity\ClaimCategory;
use AppBundle\Entity\CompanyClaimPolicies;
use AppBundle\Entity\CompanyFlexiClaimPolicies;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\ORM\Query\Expr;

class CompanyFlexiClaimPoliciesAdmin extends BaseAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $dateStart = [];
        for($i=1;$i<=31;$i++){
            $dateStart[$i]=$i;
        }
        $formMapper->add('dateStart', 'choice',
            ['choices'=>$dateStart]
            );
        $monthStart = [];
        for($i=1;$i<=12;$i++){
            $monthStart[$i]=$i;
        }
        $formMapper->add('monthStart', 'choice',['choices'=>$monthStart]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('dateStart')
            ->addIdentifier('monthStart')
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof CompanyFlexiClaimPolicies
            ? 'Flexi Claim Policies'
            : 'Company Flexi Claim Policies Management'; // shown in the breadcrumb on the create view
    }



}