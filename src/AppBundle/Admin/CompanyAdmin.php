<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Company;
use AppBundle\Entity\CostCentre;
use AppBundle\Entity\Department;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use AppBundle\Admin\BaseAdmin;
use Sonata\AdminBundle\Show\ShowMapper;

class CompanyAdmin extends BaseAdmin
{


    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', 'text',['label'=>'Company Name']);
        $formMapper->add('bizCode', 'text');
        $formMapper->add('address', 'text');
        $formMapper->add('country', 'text');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name',null,['label'=>'Company Name']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name',null,['label'=>'Company Name'])
            ->add('bizCode')
            ->add('address')
            ->add('country')
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                    'View' => array(
                        'template' => 'AppBundle:SonataAdmin/CustomActions:_list-action-company.html.twig'
                    )


                )
            ));
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $show->add('name', 'text',['label'=>'Company Name']);
        $show->add('address', 'text');
        $show->add('bizCode', 'text');
        $show->add('country', 'text');
    }

    public function toString($object)
    {
        return $object instanceof Company
            ? $object->getName()
            : 'Company Management'; // shown in the breadcrumb on the create view
    }



}