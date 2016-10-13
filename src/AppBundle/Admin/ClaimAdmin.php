<?php
namespace AppBundle\Admin;

use AppBundle\Entity\Claim;
use AppBundle\Entity\ClaimCategory;
use AppBundle\Entity\ClaimType;
use AppBundle\Entity\CompanyClaimPolicies;
use AppBundle\Entity\Position;
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
        $datagridMapper->add('createdAt', 'doctrine_orm_date_range');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('position.employeeNo', null, ['label' => 'Employee No'])
            ->add('position.user.firstName',null,['label'=>'Name'])
            ->add('companyGetClaim.name',null,['label'=>'Company'])
            ->add('claimType.code',null,['label'=>'Cost Centre'])
            ->add('claimCategory.code',null,['label'=>'Claim Category'])
            ->add('periodFrom','date',['label'=>'Period From', 'format' => 'd M Y'])
            ->add('periodTo',null,['label'=>'Period To', 'format' => 'd M Y'])
            ->add('claimAmount',null,['label'=>'Amount'])
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

    public function setCheckerAndApprover($claim)
    {
        $position = $this->getUser()->getLoginWithPosition();
        $company = $position->getCompany();
        $costCentre = $position->getCostCentre();
        $region = $position->getRegion();
        $branch = $position->getBranch();
        $department = $position->getDepartment();
        $section = $position->getSection();
        $em = $this->container->get('doctrine')->getManager();
        $checker = $em->getRepository('AppBundle\Entity\Checker')->findOneBy([
            'companySetupChecker' => $company,
            'costCentre' => $costCentre,
            'region' => $region,
            'branch' => $branch,
            'department' => $department,
            'section' => $section,
        ]);
        $approver = $em->getRepository('AppBundle\Entity\ApprovalAmountPolicies')->findOneBy([
            'companySetupApproval' => $company,
            'costCentre' => $costCentre,
            'region' => $region,
            'branch' => $branch,
            'department' => $department,
            'section' => $section,
        ]);
        $claim->setChecker($checker);
        $claim->setApprover($approver);
    }

    public function setPeriod(Claim $claim){
//        $cutOffdate = $claim->getClaimType()->
    }

    public function prePersist($object)
    {
        $this->setCheckerAndApprover($object);
        $this->addMedias($object, $object->getClaimMedias());

        $object->setPosition($this->getUser()->getLoginWithPosition());
        parent::prePersist($object); // TODO: Change the autogenerated stub
    }

    public function preUpdate($object)
    {
        $this->setCheckerAndApprover($object);
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