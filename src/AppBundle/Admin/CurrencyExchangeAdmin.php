<?php
namespace AppBundle\Admin;

use AppBundle\Entity\ClaimCategory;
use AppBundle\Entity\CurrencyExchange;
use AppBundle\Entity\CurrencyExchangeHistory;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class CurrencyExchangeAdmin extends BaseAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('code', 'text',['label'=>'Currency Code']);
        $formMapper->add('description', 'textarea',['label'=>'Currency Description']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('code',null,['label'=>'Currency Code']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('code',null,['label'=>'Currency Code'])
            ->add('description',null,['label'=>'Currency Description'])
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                    'View' => array(
                        'template' => 'AppBundle:SonataAdmin/CustomActions:_list-action-currency-exchange.html.twig'
                    )
                )
            ));
    }


    public function toString($object)
    {
        return $object instanceof CurrencyExchange
            ? $object->getCode()
            : 'Currency Exchange Management'; // shown in the breadcrumb on the create view
    }



}