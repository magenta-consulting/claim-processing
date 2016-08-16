<?php
namespace AppBundle\Admin;

use AppBundle\Entity\CostCentre;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;

class CostCentreAdmin extends AbstractAdmin
{
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getCompany()
    {
        return $this->getContainer()->get('security.token_storage')->getToken()->getUser()->getCompany();
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('code', 'text');
        $formMapper->add('description', 'textarea');
        $formMapper->add('enabled', 'checkbox', ['required' => false]);
        $formMapper->add('isDefault', 'checkbox', ['required' => false]);
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
            ->add('description')
            ->add('enabled', null, array('editable' => true))
            ->add('isDefault', null, array('editable' => true))
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof CostCentre
            ? $object->getCode()
            : 'Cost Centre Management'; // shown in the breadcrumb on the create view
    }

    /*
     * add company when add new
     */
    public function prePersist($object)
    {
        $object->setCompany($this->getCompany());
    }

    /*
     * filter by company list
     */
    public function createQuery($context = 'list')
    {
        $company = $this->getCompany();
        $query = parent::createQuery($context);
        $query->andWhere(
            $query->expr()->eq($query->getRootAliases()[0] . '.company', ':company')
        );
        $query->setParameter('company', $company);
        return $query;
    }
//    protected function configureRoutes(RouteCollection $collection)
//    {
//        // prevent display of "Add new" when embedding this form
//            $collection->remove('create');
//    }

}