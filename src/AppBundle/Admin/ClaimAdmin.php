<?php
namespace AppBundle\Admin;

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

class ClaimAdmin extends BaseAdmin
{

    public function filterCategoryBycompany(){
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('category')
            ->from('AppBundle\Entity\Category','category')
            ->where($expr->eq('category.company', ':company'))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }
    public function filterCurrencyExchangeBycompany(){
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('currencyExchange')
            ->from('AppBundle\Entity\CurrencyExchange','currencyExchange')
            ->where($expr->eq('currencyExchange.company', ':company'))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('category', 'sonata_type_model', array(
            'property' => 'name',
            'query'=>$this->filterCategoryBycompany(),
            'placeholder' => 'Select Category',
            'empty_data'  => null
        ));
        $formMapper->add('currencyExchange', 'sonata_type_model', array(
            'property' => 'code',
            'query'=>$this->filterCurrencyExchangeBycompany(),
            'placeholder' => 'Select Currency',
            'empty_data'  => null
        ));
        $formMapper->add('amount', 'number');
        $formMapper->add('purposeExpenses', 'textarea');
        $formMapper->add('receiptDate', 'date',['attr'=>['class'=>'datepicker'],'widget' => 'single_text','format' => 'MM/dd/yyyy']);
        $formMapper->add('image','sonata_media_type',[
            'provider' => 'sonata.media.provider.image',
            'context' => 'default',
            'required' => false,
            'label' => 'Image',
        ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('user.username');
        $datagridMapper->add('category.name');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('user.username',null,['lable'=>'Username'])
            ->add('category.name')
            ->add('currencyExchange.code')
            ->add('amount')
            ->add('receiptDate')
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof Claim
            ? $object->getId()
            : 'Claim'; // shown in the breadcrumb on the create view
    }



}