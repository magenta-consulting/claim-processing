<?php
namespace AppBundle\Admin;

use AppBundle\Entity\ClaimCategory;
use AppBundle\Entity\CurrencyExchange;
use AppBundle\Entity\CurrencyExchangeHistory;
use AppBundle\Entity\CurrencyExchangeValue;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class CurrencyExchangeValueAdmin extends BaseAdmin
{

    protected $parentAssociationMapping = 'currencyExchange';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('exRate', 'number');
        $formMapper->add('effectiveDate','date',['attr'=>['class'=>'datepicker'],'widget' => 'single_text','format' => 'MM/dd/yyyy']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('exRate');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('exRate')
            ->add('effectiveDate')
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof CurrencyExchangeValue
            ? $object->getCurrencyExchange()->getCode()
            : 'Currency Exchange Value Management'; // shown in the breadcrumb on the create view
    }


}