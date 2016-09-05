<?php
namespace AppBundle\Admin;

use AppBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use FOS\UserBundle\Model\UserManagerInterface;

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
        $formMapper->add('email', 'text')
        ->add('username')
        ->add('plainPassword', 'text')
        ->add('firstName', 'text')
        ->add('lastName', 'text')

        ->add('enabled', null, array('required' => false))
            ->add('image','sonata_media_type',[
            'provider' => 'sonata.media.provider.image',
            'context' => 'default',
            'required' => false,
            'label' => 'Image',
        ]);

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