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

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('historyCurrency', $this->getRouterIdParameter().'/history-currency');
    }
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('code', 'text');
        $formMapper->add('description', 'textarea');
        $formMapper->add('exRate', 'number');
        $formMapper->add('effectiveDate','date',['attr'=>['class'=>'datepicker'],'widget' => 'single_text','format' => 'MM/dd/yyyy']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('code');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('code')
            ->add('description')
            ->add('exRate')
            ->add('effectiveDate')
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                    'View' => array(
                        'template' => 'AppBundle:SonataAdmin/CustomActions:_list-action-history-currency.html.twig'
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
    public function preUpdate($object)
    {
        $history = new CurrencyExchangeHistory();
        $history->setCode($object->getCode());
        $history->setCompany($object->getCompany());
        $history->setCurrencyExchange($object);
        $history->setDescription($object->getDescription());
        $history->setEffectiveDate($object->getEffectiveDate());
        $history->setExRate($object->getExRate());
        $history->setUser($this->getUser());
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($history);
        $em->flush($history);
    }


}