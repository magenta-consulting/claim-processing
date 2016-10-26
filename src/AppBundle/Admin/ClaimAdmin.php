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
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class ClaimAdmin extends BaseAdmin
{

    public function filterClaimTypeBycompanyForUser()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('claimType')
            ->from('AppBundle\Entity\ClaimType', 'claimType')
            ->join('claimType.limitRules', 'limitRule')
            ->join('limitRule.limitRuleEmployeeGroups', 'limitRuleEmployeeGroup')
            ->where($expr->eq('claimType.company', ':company'))
            ->andWhere($expr->eq('claimType.enabled', true))
            ->andWhere($expr->eq('limitRuleEmployeeGroup.employeeGroup', ':employeeGroup'))
            ->setParameter('employeeGroup', $this->getPosition()->getEmployeeGroup())
            ->setParameter('company', $this->getCompany());
        return $qb;
    }

    public function filterClaimCategoryByClaimType($claimType)
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $rules = $em->createQueryBuilder()
            ->select('limitRule')
            ->from('AppBundle\Entity\LimitRule', 'limitRule')
            ->join('limitRule.limitRuleEmployeeGroups', 'limitRuleEmployeeGroup')
            ->where($expr->eq('limitRule.claimType', ':claimType'))
            ->andWhere($expr->eq('limitRuleEmployeeGroup.employeeGroup', ':employeeGroup'))
            ->setParameter('claimType', $claimType)
            ->setParameter('employeeGroup', $this->getPosition()->getEmployeeGroup())
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

        //step 1 (create)
        if ($this->isCurrentRoute('create')) {
            $formMapper->add('claimType', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterClaimTypeBycompanyForUser(),
                'placeholder' => 'Select Claims Type',
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
        }
        //step 2(edit)
        $subject = $this->getSubject();
        if ($subject && $subject->getId()) {
            if ($subject->getClaimType()->getCode() === 'Overseas') {
                $formMapper->add('currencyExchange', 'sonata_type_model', array(
                    'property' => 'code',
                    'query' => $this->filterCurrencyExchangeBycompany(),
                    'placeholder' => 'Select Currency',
                    'empty_data' => null,
                    'btn_add' => false,
                    'label' => 'Currency',
                    'required' => false
                ));
            }
            $formMapper->add('claimAmount', 'number', ['label' => 'Receipt Amount']);
            $formMapper->add('description', 'text', ['label' => 'Claim Description']);
            $formMapper->add('taxAmount', 'number', ['label' => 'Tax Amount', 'required' => false]);
            $formMapper->add('receiptDate', 'date', ['attr' => ['class' => 'datepicker'], 'widget' => 'single_text', 'format' => 'MM/dd/yyyy']);
            $formMapper->add('taxRate', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterTaxRateBycompany(),
                'placeholder' => 'Select Tax Code',
                'empty_data' => null,
                'btn_add' => false,
                'label' => 'Tax Code',
                'required' => false
            ));
            $formMapper->add('imageFromLibrary', 'file', array(
                'label' => 'Receipt Images',
                'required' => false,
                'mapped' => false
            ));
            $formMapper->add('imageFromCamera', 'file', array(
                'label' => 'Receipt Images',
                'required' => false,
                'mapped' => false
            ));
        }


    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $request = $this->getRequest();
        $type = $request->get('type');
        switch ($type) {
            case 'checking-each-position':
                $listMapper
                    ->add('position.employeeNo', null, ['label' => 'Employee No'])
                    ->add('position.firstName', null, ['label' => 'Name'])
                    ->add('position.costCentre.code', null, ['label' => 'Cost Centre'])
                    ->add('claimType.code', null, ['label' => 'Claim Type'])
                    ->add('claimCategory.code', null, ['label' => 'Claim Category'])
                    ->add('periodFrom', 'date', ['label' => 'Period From', 'format' => 'd M Y'])
                    ->add('periodTo', null, ['label' => 'Period To', 'format' => 'd M Y'])
                    ->add('status', null, ['label' => 'Status'])
                    ->add('createdAt', null, ['label' => 'Submission Date', 'format' => 'd M Y'])
                    ->add('claimAmount', null, ['label' => 'Amount'])
                    ->add('_action', null, array(
                        'actions' => array(
                            'show' => array(
                                'template' => 'AppBundle:SonataAdmin/CustomActions:_list-action-checker-view-claim.html.twig'
                            ),
                        )
                    ));
                break;
            default:

                $listMapper
                    ->add('claimType.code', null, ['label' => 'Claim Type'])
                    ->add('claimCategory.code', null, ['label' => 'Claim Category'])
                    ->add('claimAmount', null, ['label' => 'Amount'])
//                    ->add('a', 'debug', ['label' => 'DEBUG'])
                    ->add('_action', null, array(
                        'actions' => array(
                            'delete' => array(),
                            'show' => array(),
                            'edit' => array(
                                'template' => 'AppBundle:SonataAdmin/CustomActions:_list-action-employee-edit-claim.html.twig'
                            ),
                        )
                    ));
        }

    }


    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('uploadImage', $this->getRouterIdParameter() . '/upload-image-claim');
        $collection->add('deleteImage', $this->getRouterIdParameter() . '/{mediaId}/delete-image-claim');
        $collection->add('checkerApprove', $this->getRouterIdParameter() . '/checker-approve');
        $collection->add('checkerReject', $this->getRouterIdParameter() . '/checker-reject');
        $collection->add('firstPageCreateClaim', 'create-claim');

    }

    protected function configureBatchActions($actions)
    {
        return [];
    }

    /**
     * @param ShowMapper $show
     */
    protected function configureShowFields(ShowMapper $show)
    {
        $request = $this->getRequest();
        $type = $request->get('type');
        switch ($type) {
            case 'checker-view-claim':
                $show->tab('Claim Details')
                    ->with('Claim Details', array('class' => 'col-md-6'))
                    ->add('claimAmount', null, ['label' => 'Amount'])
                    ->add('currencyExchange.code', null, ['label' => 'Currency'])
                    ->add('claimType.code', 'text', ['label' => 'Claim Type'])
                    ->add('claimCategory.code', 'text', ['label' => 'Claim Category'])
                    ->add('status', 'text', ['label' => 'Status'])
                    ->add('receiptDate', 'date', ['label' => 'Receipt Date', 'format' => 'd M Y'])
                    ->add('submissionRemarks', null, ['label' => 'Claimant Submission Remarks'])
                    ->end()
                    ->with('Claim Images', array('class' => 'col-md-6'))
                    ->add('claimMedias', 'show_image', ['label' => 'Claim Images'])
                    ->end()
                    ->end()
                    ->tab('Submission, Employment Details')
                    ->with('Submission Details', array('class' => 'col-md-6'))
//                    ->add('position.firstName',null,['label'=>'Submitted By'])
                    ->add('createdAt', null, ['label' => 'Date Submitted', 'format' => 'd M Y'])
                    ->add('position.firstName', null, ['label' => 'Claimant First Name'])
                    ->add('position.lastName', null, ['label' => 'Claimant Last Name'])
                    ->add('position.employeeNo', null, ['label' => 'Employee No.'])
                    ->add('position.contactNumber', null, ['label' => 'Contact No.'])
                    ->end()
                    ->with('Employment Details', array('class' => 'col-md-6'))
                    ->add('position.company.name', null, ['label' => 'Company'])
                    ->add('position.costCentre.code', null, ['label' => 'Cost Centre'])
                    ->add('position.region.code', null, ['label' => 'Region'])
                    ->add('position.branch.code', null, ['label' => 'Branch'])
                    ->add('position.department.code', null, ['label' => 'Department'])
                    ->add('position.section.code', null, ['label' => 'Section'])
                    ->add('position.employeeType.code', null, ['label' => 'Employee Type'])
                    ->add('position.employmentType.code', null, ['label' => 'Employment Type'])
                    ->end()
                    ->end();
                break;
            case 'employee-preview-claim':
                $show
                    ->with('Claim Images', array('class' => 'col-md-6'))
                    ->add('claimMedias', 'show_image', ['label' => 'Claim Images'])
                    ->end()
                    ->with('Claim Details', array('class' => 'col-md-6'))
                    ->add('description', 'text', ['label' => 'Description'])
                    ->add('claimType.code', 'text', ['label' => 'Claim Type'])
                    ->add('claimCategory.code', 'text', ['label' => 'Claim Category'])
                    ->add('claimAmount', null, ['label' => 'Receipt Amount'])
                    ->add('receiptDate', 'date', ['label' => 'Receipt Date', 'format' => 'd M Y'])
                    ->add('currencyExchange.code', null, ['label' => 'Currency'])
                    ->add('1', null, ['label' => 'Conversion Value'])
                    ->add('taxRate.code', null, ['label' => 'Tax Code'])
                    ->add('taxAmount', null, ['label' => 'Tax Amount'])
                    ->end();
                break;
            default:
                $show
                    ->with('Claim Images', array('class' => 'col-md-6'))
                    ->add('claimMedias', 'show_image', ['label' => 'Claim Images'])
                    ->end()
                    ->with('Claim Details', array('class' => 'col-md-6'))
                    ->add('claimType.code', 'text', ['label' => 'Claim Type'])
                    ->add('claimCategory.code', 'text', ['label' => 'Claim Category'])
                    ->add('claimAmount', null, ['label' => 'Amount'])
                    ->add('taxRate.code', null, ['label' => 'Tax Code'])
                    ->add('taxAmount', null, ['label' => 'Tax Amount'])
                    ->add('currencyExchange.code', null, ['label' => 'Currency'])
                    ->add('1', null, ['label' => 'Ex Rate'])
                    ->add('2', 'show_claim_limit', ['label' => 'Claim Limit'])
                    ->add('status', 'text', ['label' => 'Status'])
                    ->add('receiptDate', 'date', ['label' => 'Receipt Date', 'format' => 'd M Y'])
                    ->end()
                    ->with('Claimant Submission Remarks', array('class' => 'col-md-6'))
                    ->add('submissionRemarks', 'show_claim_submission_remarks')
                    ->end()
                    ->with('Checker', array('class' => 'col-md-6'))
                    ->add('checker', 'show_checker', ['label' => 'Company'])
                    ->end()
                    ->with('Approver', array('class' => 'col-md-6'))
                    ->add('approver', 'show_approver', ['label' => 'Company'])
                    ->end();
                break;

        }

    }

    public function getNewInstance()
    {
        $object = parent::getNewInstance();
        $object->setClaimType($this->getContainer()->get('app.claim_rule')->getClaimTypeDefault());
        return $object;
    }

    public function manualUpdate($claim)
    {
        $claim->setPosition($this->getPosition());
    }


    public function toString($object)
    {
        return $object instanceof Claim
            ? $object->getId()
            : 'Claim'; // shown in the breadcrumb on the create view
    }


}