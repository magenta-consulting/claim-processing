<?php
namespace AppBundle\Admin;

use AppBundle\Admin\Transformer\RolesTransformer;
use AppBundle\Entity\Position;
use AppBundle\Entity\PositionSubmitter;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\Expr;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\CoreBundle\Validator\ErrorElement;

class PositionSubmitterAdmin extends BaseAdmin
{



    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('submissionForPosition','sonata_type_model_list', ['btn_add' => false]);

    }


    public function toString($object)
    {
        return 'Proxy Position';
    }
}