<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Company;
use AppBundle\Entity\CostCentre;
use AppBundle\Entity\PayCodeType;
use AppBundle\Entity\Region;
use Doctrine\ORM\Query\Expr;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use AppBundle\Entity\User;

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

    public function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            return;
        }

        $tokenStorage = $this->container->get('security.token_storage');

        if (!$token = $tokenStorage->getToken()) {
            return;
        }

        $user = $token->getUser();
        if (!is_object($user)) {
            return;
        }

        return $user;
    }

    public function getCompany()
    {
        return $this->getContainer()->get('security.token_storage')->getToken()->getUser()->getCompany();
    }

    public function isAdmin()
    {
        if ($this->getUser()) {
            if ($this->getUser()->hasRole('ROLE_ADMIN')) {
                return true;
            }
        }
        return false;
    }

    public function isCLient()
    {
        if ($this->getUser()) {
            if ($this->getUser()->hasRole('ROLE_CLIENT_ADMIN')) {
                return true;
            }
        }
        return false;
    }

    public function isCs()
    {
        if ($this->getUser()) {
            if ($this->getUser()->hasRole('ROLE_CS_ADMIN')) {
                return true;
            }
        }
        return false;
    }

    public function isHr()
    {
        if ($this->getUser()) {
            if ($this->getUser()->hasRole('ROLE_HR_ADMIN')) {
                return true;
            }
        }
        return false;
    }

    /*
    * add company when add new(not effect when update)
    */
    public function prePersist($object)
    {
        if ($this->isAdmin()) {
            //admin current only create a client company (parent = null)
            if ($object instanceof Company) {
                $em = $this->getContainer()->get('doctrine')->getManager();
                //add Pay Code Type: By system default always has “Deductions” and “Allowances”
                $payCodeType1 = new PayCodeType();
                $payCodeType1->setName('Deductions');
                $payCodeType1->setOrderSort(1);
                $payCodeType1->setEnabled(true);
                $payCodeType1->setCompany($object);
                $em->persist($payCodeType1);

                $payCodeType2 = new PayCodeType();
                $payCodeType2->setName('Allowances');
                $payCodeType2->setOrderSort(1);
                $payCodeType2->setEnabled(true);
                $payCodeType2->setCompany($object);
                $em->persist($payCodeType2);
                $em->flush();

            }
        }
        if ($this->isCLient()) {
            if (!$object instanceof Company) {
                if (!$object instanceof User) {
                    $object->setCompany($this->getCompany());
                }
            } elseif ($object instanceof Company) {
                if ($object->getId() !== $this->getCompany()->getId()) {
                    $object->setParent($this->getCompany());
                }
            }
        }
        if ($this->isHr()) {
            $object->setCompany($this->getCompany());
        }
    }

    /*
     * filter by company list
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $class = $this->getClass();
        $company = $this->getCompany();
        $expr = new Expr();
        if ($this->isCLient()) {
            if ($company instanceof $class) {
                $query->andWhere(
                    $expr->orX(
                        $expr->eq($query->getRootAliases()[0] . '.parent', ':company'),
                        $expr->eq($query->getRootAliases()[0], ':company')
                    )
                );

                $query->setParameter('company', $company);
            } else {
                if ($this->getClass() !== 'AppBundle\Entity\User') {
                    $query->andWhere(
                        $expr->eq($query->getRootAliases()[0] . '.company', ':company')
                    );
                    $query->setParameter('company', $company);
                }

            }
        }
        if ($this->isHr()) {
            $query->andWhere(
                $expr->eq($query->getRootAliases()[0] . '.company', ':company')
            );
            $query->setParameter('company', $company);
        }

        return $query;
    }


}