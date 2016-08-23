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

    public function filterClaimTypeBycompany(){
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('claimType')
            ->from('AppBundle\Entity\ClaimType','claimType')
            ->where($expr->eq('claimType.company', ':company'))
            ->andWhere($expr->eq('claimType.enabled', true))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }
    public function filterClaimCategoryBycompany(){
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('claimCategory')
            ->from('AppBundle\Entity\ClaimCategory','claimCategory')
            ->where($expr->eq('claimCategory.company', ':company'))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }
    public function filterTaxRateBycompany(){
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('taxRate')
            ->from('AppBundle\Entity\TaxRate','taxRate')
            ->where($expr->eq('taxRate.company', ':company'))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }
    public function filterPayCostBycompany(){
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('payCode')
            ->from('AppBundle\Entity\PayCode','payCode')
            ->where($expr->eq('payCode.company', ':company'))
            ->andWhere($expr->eq('payCode.enabled', true))
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
        $formMapper->add('claimType', 'sonata_type_model', array(
            'property' => 'code',
            'query'=>$this->filterClaimTypeBycompany(),
            'placeholder' => 'Select Type',
            'empty_data'  => null
        ));
        $formMapper->add('claimCategory', 'sonata_type_model', array(
            'property' => 'code',
            'query'=>$this->filterClaimCategoryBycompany(),
            'placeholder' => 'Select Category',
            'empty_data'  => null
        ));

        $formMapper->add('payCode', 'sonata_type_model', array(
            'property' => 'code',
            'query'=>$this->filterPayCostBycompany(),
            'placeholder' => 'Select Pay Code',
            'empty_data'  => null
        ));
        $formMapper->add('taxRate', 'sonata_type_model', array(
            'property' => 'code',
            'query'=>$this->filterTaxRateBycompany(),
            'placeholder' => 'Select Tax Rate',
            'empty_data'  => null,
            'required'=>false
        ));
//        $formMapper->add('currencyExchange', 'sonata_type_model', array(
//            'property' => 'code',
//            'query'=>$this->filterCurrencyExchangeBycompany(),
//            'placeholder' => 'Select Currency',
//            'empty_data'  => null
//        ));
//        $formMapper->add('amount', 'number');
//        $formMapper->add('purposeExpenses', 'textarea');
//        $formMapper->add('receiptDate', 'date',['attr'=>['class'=>'datepicker'],'widget' => 'single_text','format' => 'MM/dd/yyyy']);
//        $formMapper->add('image','sonata_media_type',[
//            'provider' => 'sonata.media.provider.image',
//            'context' => 'default',
//            'required' => false,
//            'label' => 'Image',
//        ]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
//        $datagridMapper->add('user.username');
        $datagridMapper->add('claimType.code');
        $datagridMapper->add('claimCategory.code',null,['label'=>'Category Code']);
        $datagridMapper->add('taxRate.code',null,['label'=>'Tax Code']);
        $datagridMapper->add('payCode.code',null,['label'=>'Pay Code']);
//        $datagridMapper->add('currencyExchange.code');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
//            ->addIdentifier('user.username',null,['lable'=>'aa'])
            ->addIdentifier('claimType.code')
            ->add('claimType.claimTypeType.name',null,['label'=>'Claim Type'])
            ->add('claimCategory.code',null,['label'=>'Category Code'])
            ->add('claimCategory.description',null,['label'=>'Category Description'])
            ->add('taxRate.code',null,['label'=>'Tax Code'])
            ->add('taxRate.description',null,['label'=>'Tax Description'])
            ->add('payCode.code',null,['label'=>'Pay Code'])
//            ->add('currencyExchange.code')
//            ->add('amount')
//            ->add('receiptDate')
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof Claim
            ? $object->getClaimType()->getCode()
            : 'Claim Category'; // shown in the breadcrumb on the create view
    }



}