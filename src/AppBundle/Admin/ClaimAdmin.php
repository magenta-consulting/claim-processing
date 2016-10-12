<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Claim;
use AppBundle\Entity\ClaimCategory;
use AppBundle\Entity\ClaimType;
use AppBundle\Entity\CompanyClaimPolicies;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\ORM\Query\Expr;
use AppBundle\Admin\BaseAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class ClaimAdmin extends BaseAdmin
{

    public function filterClaimCategoryByClaimType($claimType)
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $rules = $em->createQueryBuilder()
            ->select('category')
            ->from('AppBundle\Entity\Category', 'category')
            ->where($expr->eq('category.claimType', ':claimType'))
            ->setParameter('claimType', $claimType)
            ->getQuery()
            ->getResult();
        $listCategory = [];
        foreach ($rules as $rule) {
            $listCategory[] = $rule->getClaimCategory()->getId();
        }
        $listCategory = count($listCategory) ? $listCategory : [0];
        $qb->select('claimCategory')
            ->from('AppBundle\Entity\ClaimCategory', 'claimCategory')
            ->where($expr->eq('claimCategory.company', ':company'))
            ->setParameter('company', $this->getCompany())
            ->andWhere($expr->in('claimCategory.id', $listCategory));//if $listCategory
        return $qb;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('companyGetClaim', 'sonata_type_model', array(
            'property' => 'name',
            'query' => $this->filterCompanyBycompany(),
            'placeholder' => 'Select Company',
            'empty_data' => null,
            'label' => 'Company',
            'data' => $this->getCompany(),
            'btn_add' => false
        ));
        $formMapper->add('claimType', 'sonata_type_model', array(
            'property' => 'code',
            'query' => $this->filterClaimTypeBycompany(),
            'placeholder' => 'Select Type',
            'empty_data' => null,
            'btn_add' => false
        ));
        $formModifier = function (FormInterface $form, $claimType = null) {
            $form->add('claimCategory', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterClaimCategoryByClaimType($claimType),
                'placeholder' => 'Select Category',
                'empty_data' => null,
                'btn_add' => false,
                'label' => 'Category',
                'model_manager' => $this->getModelManager(),
                'class' => 'AppBundle\Entity\ClaimCategory'
            ));
        };
        $formMapper->getFormBuilder()->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $claim = $event->getData();
                $claimType = $claim === null ? null : $claim->getClaimType();
                $formModifier($event->getForm(), $claimType);
            }
        );
        $formMapper->getFormBuilder()->get('claimType')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $claimType = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $claimType);
            }
        );

        $formMapper->add('gst', ChoiceType::class, [
            'choices' => array(
                'No' => false,
                'Yes' => true,
            ),
            'label' => 'GST'
        ]);

        $formMapper->add('claimAmount', 'number', ['label' => 'Claim Amount', 'required' => false]);
        $formMapper->add('gstAmount', 'number', ['label' => 'GST Amount', 'required' => false]);
        $formMapper->add('amountWithoutGst', 'number', ['label' => 'Amount Without GST', 'required' => false]);


        $formMapper->add('currencyExchange', 'sonata_type_model', array(
            'property' => 'code',
            'query' => $this->filterCurrencyExchangeBycompany(),
            'placeholder' => 'Select Currency',
            'empty_data' => null,
            'btn_add' => false,
            'label' => 'Currency',
            'required' => false
        ));
        $formMapper->add('receiptDate', 'date', ['attr' => ['class' => 'datepicker'], 'widget' => 'single_text', 'format' => 'MM/dd/yyyy'])
            ->add('submissionRemarks', 'textarea', ['required' => false])
            ->add('claimMedias', 'sonata_type_collection', array(
                'label' => ' ',
                'required' => false,
            ),
                array(
                    'edit' => 'inline',
                    'inline' => 'table',
                ));


    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('position.employeeNo', null, ['lable' => 'Username'])
            ->add('position.user.firstName')
            ->add('companyGetClaim.name')
            ->add('claimType.code')
            ->add('claimCategory.code')
            ->add('createdAt')
            ->add('claimAmount')
            ->add('_action', null, array(
                'actions' => array(
                    'delete' => array(),
                    'show' => array(),
                )
            ));
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $show->add('position.user.firstName', 'text', ['label' => 'Company Name']);
        $show->add('companyGetClaim.name', 'text');
        $show->add('claimType.code', 'text');
        $show->add('claimCategory.code', 'text');
    }

    private function addMedias($claim, $medias)
    {
        foreach ($medias as $media) {
            $claim->addClaimMedia($media);
        }
    }

    public function prePersist($object)
    {
        $this->addMedias($object, $object->getClaimMedias());

        $object->setPosition($this->getUser()->getLoginWithPosition());
        parent::prePersist($object); // TODO: Change the autogenerated stub
    }

    public function preUpdate($object)
    {
        $this->addMedias($object, $object->getClaimMedias());
        parent::preUpdate($object); // TODO: Change the autogenerated stub
    }

    public function toString($object)
    {
        return $object instanceof Claim
            ? $object->getId()
            : 'Claim'; // shown in the breadcrumb on the create view
    }


}