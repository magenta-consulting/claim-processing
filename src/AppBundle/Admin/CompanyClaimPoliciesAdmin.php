<?php
namespace AppBundle\Admin;

use AppBundle\Entity\ClaimCategory;
use AppBundle\Entity\CompanyClaimPolicies;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\ORM\Query\Expr;

class CompanyClaimPoliciesAdmin extends BaseAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $cutOffDate = [];
        for($i=1;$i<=31;$i++){
            $cutOffDate[$i]=$i;
        }
        $formMapper->add('cutOffDate', 'choice',
            ['choices'=>$cutOffDate]
            );
        $claimablePeriod = [];
        for($i=1;$i<=12;$i++){
            $claimablePeriod[$i]=$i;
        }
        $formMapper->add('claimablePeriod', 'choice',['label'=>'Claimable Period (months)','choices'=>$claimablePeriod]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('cutOffDate')
            ->addIdentifier('claimablePeriod',null,['label'=>'Claimable Period (months)'])
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof CompanyClaimPolicies
            ? 'Claim Policies'
            : 'Company Claim Policies Management'; // shown in the breadcrumb on the create view
    }



}