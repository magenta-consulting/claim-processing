<?php
namespace AppBundle\Admin;

use AppBundle\Admin\Transformer\RolesTransformer;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\Expr;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserAdmin extends BaseAdmin
{
    protected $parentAssociationMapping = 'company';

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
        $company =1;
        $formMapper
            ->tab('Personal Particulars')
            ->with('Group A',array('class' => 'col-md-6'))
            ->add('alias', 'text')
            ->add('firstName', 'text')
            ->add('lastName', 'text')
            ->add('email', 'text')
            ->end()
            ->with('Group B',array('class' => 'col-md-6'))
            ->add('employeeNo', 'number')
            ->add('contactNumber', 'number')
            ->add('nric', 'number')
            ->end()
            ->end()
            /**-------------------**/
            ->tab('Employment Details')
            ->with('Group A',array('class' => 'col-md-6'))
            ->add('employeeType', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterEmployeeTypeBycompany(),
                'placeholder' => 'Select Employee Type',
                'empty_data' => null,
                'required' => false,
                'btn_add' => false
            ))
            ->add('employmentType', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterEmploymentTypeBycompany(),
                'placeholder' => 'Select Employment Type',
                'empty_data' => null,
                'required' => false,
                'btn_add' => false
            ))
            ->add('dateJoined', 'date',['attr'=>['class'=>'datepicker'],'widget' => 'single_text','format' => 'MM/dd/yyyy'])
            ->add('probation', 'number', array())
            ->add('lastDateOfService', 'date',['attr'=>['class'=>'datepicker'],'widget' => 'single_text','format' => 'MM/dd/yyyy'])
            ->end()
            ->with('Group B',array('class' => 'col-md-6'))
            ->add('costCentre', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterCostCentreBycompany(),
                'placeholder' => 'Select Cost Centre',
                'empty_data' => null,
                'btn_add' => false
            ))
            ->add('region', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterRegionBycompany(),
                'placeholder' => 'Select Region',
                'empty_data' => null,
                'required' => false,
                'btn_add' => false
            ))
            ->add('branch', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterBranchBycompany(),
                'placeholder' => 'Select Branch',
                'empty_data' => null,
                'required' => false,
                'btn_add' => false
            ))
            ->add('section', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterSectionBycompany(),
                'placeholder' => 'Select Section',
                'empty_data' => null,
                'required' => false,
                'btn_add' => false
            ))
            ->end()
            ->end()
            /**-------------------**/
            ->tab('User Account Info')
            ->with('User Account Info')
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
            ->end()
//            /**-------------------**/
            ->tab('Claims Approver Details')
            ->with('Claims Approver Details')

            ->end()
            ->end()
////            /**-------------------**/
            ->tab('Appointed Proxy Submitter')
            ->with('Appointed Proxy Submitter')
            ->add('proxySubmiters', 'sonata_type_model_autocomplete', array(
                'property' => 'email',
                'multiple'=>true,
            ))
            ->end()
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