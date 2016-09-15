<?php
namespace AppBundle\Admin;

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

class PayCodeAdmin extends BaseAdmin
{
    public function filterPayCodeTypeBycompany(){
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('payCodeType')
            ->from('AppBundle\Entity\PayCodeType','payCodeType')
            ->where($expr->eq('payCodeType.company', ':company'))
            ->andWhere($expr->eq('payCodeType.enabled', true))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('code', 'text',['label'=>'Pay Code']);
        $formMapper->add('description', 'textarea',['label'=>'Pay Code Description']);
        $formMapper->add('payCodeType', 'sonata_type_model', array(
            'property' => 'name',
            'query'=>$this->filterPayCodeTypeBycompany(),
            'placeholder' => 'Select Type',
            'empty_data'  => null,
            'label'=>'Type'
        ));
        $formMapper->add('enabled', 'checkbox', ['required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('code',null,['label'=>'Pay Code'])
            ->add('enabled')
            ->add('payCodeType.name',null,['label'=>'Type']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('code',null,['label'=>'Pay Code'])
            ->add('description',null,['label'=>'Pay Code Description'])
            ->add('payCodeType.name',null,['label'=>'Type'])
            ->add('enabled', null, array('editable' => true))
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof PayCode
            ? $object->getCode()
            : 'Pay Code Management'; // shown in the breadcrumb on the create view
    }

}