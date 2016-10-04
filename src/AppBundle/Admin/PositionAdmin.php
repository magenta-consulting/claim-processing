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
use Sonata\CoreBundle\Validator\ErrorElement;

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
    private function addSubmissionBy($position,$submissionBy)
    {
        foreach ($submissionBy as $submissionBy) {
            $position->addSubmissionBy($submissionBy);
        }
    }

    public function preUpdate($position)
    {
        //user
        $user = $this->updateUser();
        $position->setUser($user);
        //proxy position(bug sonata admin)
        $this->addSubmissionBy($position,$position->getSubmissionBy());

    }

    public function prePersist($position)
    {
        parent::prePersist($position);
        //user
        $user = $this->updateUser();
        $position->setUser($user);
        //proxy position(bug sonata admin)
        $this->addSubmissionBy($position,$position->getSubmissionBy());
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->hasParentFieldDescription()) {
            $parameters = $this->getParentFieldDescription()->getOption('link_parameters', array());
        }else{
            $parameters=[];
        }
        $id = $this->getRequest()->get($this->getIdParameter());
        $object = $this->getObject($id);
        $action = $object === null ? 'create' : 'edit';
        if ($this->getCompany() === null || $this->getCompany()->getParent() === null) {
            //if this is a company client or user login is admin will have 3 role
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
                ->add('alias', 'text', ['required' => false])
                ->add('firstName', 'text')
                ->add('lastName', 'text')
                ->add('email', 'text')
                ->end()
                ->with('Group B', array('class' => 'col-md-6'))
                ->add('employeeNo', 'number')
                ->add('contactNumber', 'number', ['required' => false])
                ->add('nric', 'number', ['label' => 'NRIC/Fin No'])
                ->end()
                ->end();
            /**-------------------**/
            if ($this->isCLient() || $this->isHr()) {
                $formMapper->tab('Employment Details')
                    ->with('Group A', array('class' => 'col-md-6'))
                    ->add('employeeType', 'sonata_type_model', array(
                        'property' => 'code',
                        'query' => $this->filterEmployeeTypeBycompany(),
                        'placeholder' => 'Select Employee Type',
                        'empty_data' => null,
                        'btn_add' => false,
                    ))
                    ->add('employmentType', 'sonata_type_model', array(
                        'property' => 'code',
                        'query' => $this->filterEmploymentTypeBycompany(),
                        'placeholder' => 'Select Employment Type',
                        'empty_data' => null,
                        'btn_add' => false,
                    ))
                    ->add('dateJoined', 'date', ['attr' => ['class' => 'datepicker'], 'widget' => 'single_text', 'format' => 'MM/dd/yyyy', 'required' => false])
                    ->add('probation', 'number', ['label' => 'Probation (Month)', 'required' => false])
                    ->add('lastDateOfService', 'date', ['attr' => ['class' => 'datepicker'], 'widget' => 'single_text', 'format' => 'MM/dd/yyyy', 'required' => false])
                    ->end()
                    ->with('Group B', array('class' => 'col-md-6'))
                    ->add('costCentre', 'sonata_type_model', array(
                        'property' => 'code',
                        'query' => $this->filterCostCentreBycompany(),
                        'placeholder' => 'Select Cost Centre',
                        'empty_data' => null,
                        'btn_add' => false,
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
                    ->end();
            }

            /**-------------------**/
            $formMapper->tab('User Account Info')
                ->with('User Account Info')
                ->add('roles', 'choice', [
                    'choices' => $roles
                ])
                ->add('plainPassword', 'text', [
                    'mapped' => false,
                    'required' => ($action === 'edit' ? false : true)
                ])
                ->end()
                ->end();

            if ($this->isCLient() || $this->isHr()) {
                /**-------------------**/
                $formMapper->tab('Claims Approver Details')
                    ->with('Claims Approver Details')
                    ->end()
                    ->end();
            }

            /**-------------------**/
            $formMapper->tab('Appointed Proxy Submitter')
                ->with('Appointed Proxy Submitter')

                ->add('submissionBy', 'sonata_type_collection', array('required' => false,
                ), array(
                        'edit' => 'inline',
                        'inline' => 'table',
//                        'sortable' => 'email',
                        'link_parameters' => [],
                        'admin_code' => 'admin.position_submitter',
                    )
                )
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
            ->add('nric', null, ['label' => 'NRIC/Fin No'])
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