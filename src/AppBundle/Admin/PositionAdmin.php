<?php
namespace AppBundle\Admin;

use AppBundle\Admin\Transformer\RolesTransformer;
use AppBundle\Entity\Position;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\Expr;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PositionAdmin extends BaseAdmin
{
    protected $parentAssociationMapping = 'company';

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

    public function updateUser()
    {
        $plainPassword = $this->getForm()->get('plainPassword')->getData();
        $email = $this->getForm()->get('email')->getData();
        $user = $this->getUserManager()->findUserByEmail($email);
        if (!$user) {
            $user = $this->getUserManager()->createUser();
            $user->setUsername($email);
            $user->setEmail($email);
            $user->setPlainPassword($plainPassword);
            $user->setEnabled(true);
            $this->getUserManager()->updateUser($user);
        } else {
            if (!empty($plainPassword)) {
                $this->getUserManager()->updateCanonicalFields($user);
                $this->getUserManager()->updatePassword($user);
            }
        }
        return $user;
    }

    public function preUpdate($position)
    {
        $user = $this->updateUser();
        $position->setUser($user);
    }

    public function prePersist($position)
    {
        $user = $this->updateUser();
        $position->setUser($user);
        parent::prePersist($position);
    }


    protected function configureFormFields(FormMapper $formMapper)
    {
        $id = $this->getRequest()->get($this->getIdParameter());
        $object = $this->getObject($id);
        $action = $object === null ? 'create' : 'edit';
        $require = $this->isAdmin() ? false:true;
        if ($this->getCompany()->getParent()===null) {
            //if this is a company client will have 3 role
            $roles = [
                'ROLE_CLIENT_ADMIN' => 'ROLE_CLIENT_ADMIN',
                'ROLE_HR_ADMIN' => 'ROLE_HR_ADMIN',
                'ROLE_USER' => 'ROLE_USER',
            ];
        } else {
            //if this is a sub company will have 2 role
            $roles = [
                'ROLE_HR_ADMIN' => 'ROLE_HR_ADMIN',
                'ROLE_USER' => 'ROLE_USER',
            ];
        }
        $formMapper
            ->tab('Personal Particulars')
            ->with('Group A', array('class' => 'col-md-6'))
            ->add('alias', 'text',['required' => false])
            ->add('firstName', 'text')
            ->add('lastName', 'text')
            ->add('email', 'text')
            ->end()
            ->with('Group B', array('class' => 'col-md-6'))
            ->add('employeeNo', 'number')
            ->add('contactNumber', 'number',['required' => false])
            ->add('nric', 'number',['label'=>'NRIC/Fin No'])
            ->end()
            ->end()
            /**-------------------**/
            ->tab('Employment Details')
            ->with('Group A', array('class' => 'col-md-6'))
            ->add('employeeType', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterEmployeeTypeBycompany(),
                'placeholder' => 'Select Employee Type',
                'empty_data' => null,
                'btn_add' => false,
                'required'=>$require
            ))
            ->add('employmentType', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterEmploymentTypeBycompany(),
                'placeholder' => 'Select Employment Type',
                'empty_data' => null,
                'btn_add' => false,
                'required'=>$require
            ))
            ->add('dateJoined', 'date', ['attr' => ['class' => 'datepicker'], 'widget' => 'single_text', 'format' => 'MM/dd/yyyy','required'=>false])
            ->add('probation', 'number',['label'=>'Probation (Month)','required'=>false])
            ->add('lastDateOfService', 'date', ['attr' => ['class' => 'datepicker'], 'widget' => 'single_text', 'format' => 'MM/dd/yyyy','required'=>false])
            ->end()
            ->with('Group B', array('class' => 'col-md-6'))
            ->add('costCentre', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterCostCentreBycompany(),
                'placeholder' => 'Select Cost Centre',
                'empty_data' => null,
                'btn_add' => false,
                'required'=>$require
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
            ->add('roles', 'choice', [
                'choices' => $roles
            ])
            ->add('plainPassword', 'text', [
                'mapped' => false,
                'required' => ($action === 'edit' ? false : true)
            ])
            ->end()
            ->end()
            /**-------------------**/
            ->tab('Claims Approver Details')
            ->with('Claims Approver Details')
            ->end()
            ->end()
            /**-------------------**/
            ->tab('Appointed Proxy Submitter')
            ->with('Appointed Proxy Submitter')
            ->add('proxySubmiters', 'sonata_type_model_autocomplete', array(
                'property' => 'email',
                'multiple' => true,
                'required' => false,
            ))
            ->end()
            ->end();
        $formMapper->get('roles')->addModelTransformer(new RolesTransformer());

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('email');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('employeeNo', null, array(
                'sortable' => 'email',
            ))->add('email', null, array(
                'sortable' => 'email',
            ))
            ->add('firstName')
            ->add('lastName')
            ->add('contactNumber')
            ->add('nric',null,['label'=>'NRIC/Fin No'])
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof Position
            ? $object->getEmail()
            : 'User'; // shown in the breadcrumb on the create view
    }
}