<?php
namespace AppBundle\Admin;

use AppBundle\Entity\ClaimType;
use AppBundle\Entity\CostCentre;
use AppBundle\Entity\Region;
use AppBundle\Entity\TaxRate;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\ORM\Query\Expr;

class ClaimTypeAdmin extends BaseAdmin
{
    public function filterClaimTypeTypeBycompany(){
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('claimTypeType')
            ->from('AppBundle\Entity\ClaimTypeType','claimTypeType')
            ->where($expr->eq('claimTypeType.company', ':company'))
            ->andWhere($expr->eq('claimTypeType.enabled', true))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('code', 'text');
        $formMapper->add('enabled', 'checkbox', ['required' => false]);
        $formMapper->add('claimTypeType', 'sonata_type_model', array(
            'property' => 'name',
            'query'=>$this->filterClaimTypeTypeBycompany(),
            'placeholder' => 'Select Type',
            'empty_data'  => null
        ));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('code')
            ->add('enabled');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('code')
            ->add('claimTypeType.name')
            ->add('enabled', null, array('editable' => true))
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof ClaimType
            ? $object->getCode()
            : 'Claim Type Management'; // shown in the breadcrumb on the create view
    }



}