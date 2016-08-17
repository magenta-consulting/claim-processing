<?php
namespace AppBundle\Admin;

use AppBundle\Entity\CostCentre;
use AppBundle\Entity\Region;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;

class BaseAdmin extends AbstractAdmin
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



}