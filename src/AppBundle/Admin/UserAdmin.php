<?php
namespace AppBundle\Admin;

use AppBundle\Admin\Transformer\RolesTransformer;
use AppBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use FOS\UserBundle\Model\UserManagerInterface;

class UserAdmin extends BaseAdmin
{
    protected $parentAssociationMapping = 'company';


    public function filterCostCentreBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('costCentre')
            ->from('AppBundle\Entity\CostCentre', 'costCentre')
            ->where($expr->eq('costCentre.company', ':company'))
            ->andWhere($expr->eq('costCentre.enabled', true))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }

    public function filterRegionBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('region')
            ->from('AppBundle\Entity\Region', 'region')
            ->where($expr->eq('region.company', ':company'))
            ->andWhere($expr->eq('region.enabled', true))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }

    public function filterBranchBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('branch')
            ->from('AppBundle\Entity\Branch', 'branch')
            ->where($expr->eq('branch.company', ':company'))
            ->andWhere($expr->eq('branch.enabled', true))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }
    public function filterSectionBycompany(){
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('section')
            ->from('AppBundle\Entity\Section','section')
            ->where($expr->eq('section.company', ':company'))
            ->andWhere($expr->eq('section.enabled', true))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }
    public function filterEmployeeTypeBycompany(){
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('employeeType')
            ->from('AppBundle\Entity\EmployeeType','employeeType')
            ->where($expr->eq('employeeType.company', ':company'))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }
    public function filterEmploymentTypeBycompany(){
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('employmentType')
            ->from('AppBundle\Entity\EmploymentType','employmentType')
            ->where($expr->eq('employmentType.company', ':company'))
            ->setParameter('company', $this->getCompany());
        return $qb;
    }
    public function preUpdate($user)
    {
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
    }

    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Personal Particulars', array('class' => 'col-md-6'))
            ->add('alias', 'text')
            ->add('firstName', 'text')
            ->add('lastName', 'text')
            ->add('username')
            ->add('email', 'text')
            ->add('employeeNo', 'number')
            ->add('contactNumber', 'number')
            ->add('nric', 'number')
            ->end()
            /**-------------------**/
            ->with('Employment Details', array('class' => 'col-md-6'))
            ->end()
            /**-------------------**/
            ->with('User Account Info', array('class' => 'col-md-6'))
//                   if ($this->isAdmin() || $this->isCLient()) {
            ->add('roles', 'choice', [
                'choices' => [
                    'ROLE_CLIENT_ADMIN' => 'ROLE_CLIENT_ADMIN',
                    'ROLE_HR_ADMIN' => 'ROLE_HR_ADMIN',
                    'ROLE_USER' => 'ROLE_USER',
                ],
            ])
//                   }
//
            ->add('plainPassword', 'text')
            ->add('enabled', null, array('required' => false))
            ->end()
//            /**-------------------**/
            ->with('Claims Approver Details', array('class' => 'col-md-6'))
            ->end()
//            /**-------------------**/
            ->with('Appointed Proxy Submitter', array('class' => 'col-md-6'))
            ->end();
        $formMapper->get('roles')->addModelTransformer(new RolesTransformer());


    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('email')
            ->add('enabled');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('email', null, array(
                'sortable' => 'email',
            ))
            ->add('username')
            ->add('firstName')
            ->add('lastName')
            ->add('image')
            ->add('enabled', null, array('editable' => true))
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof User
            ? $object->getEmail()
            : 'User'; // shown in the breadcrumb on the create view
    }
}