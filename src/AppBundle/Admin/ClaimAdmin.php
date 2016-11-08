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

    public function getPosition()
    {
        if ($this->getRequest()->get('type') == 'onbehalf') {
            $em = $this->container->get('doctrine')->getManager();
            $positionId = $this->getRequest()->get('position-id');
            $position = $em->getRepository('AppBundle\Entity\Position')->find($positionId);
            return $position;
        }
        return parent::getPosition(); // TODO: Change the autogenerated stub
    }

    public function filterClaimTypeBycompanyForUser()
    {
        $position = $this->getPosition();
        $em = $this->container->get('doctrine')->getManager();
        $employeeGroupBelongUser = $this->getContainer()->get('app.claim_rule')->getEmployeeGroupBelongToUser($position);
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('claimType')
            ->from('AppBundle\Entity\ClaimType', 'claimType')
            ->join('claimType.limitRules', 'limitRule')
            ->join('limitRule.limitRuleEmployeeGroups', 'limitRuleEmployeeGroup')
            ->join('limitRuleEmployeeGroup.employeeGroup', 'employeeGroup')
            ->where($expr->eq('claimType.company', ':company'))
            ->andWhere($expr->eq('claimType.enabled', true))
            ->andWhere($expr->in('employeeGroup.description', ':employeeGroupBelongUser'))
            ->setParameter('employeeGroupBelongUser', $employeeGroupBelongUser)
            ->setParameter('company', $this->getClientCompany());
        return $qb;
    }

    public function filterClaimCategoryByClaimType($claimType)
    {
        $position = $this->getPosition();
        $em = $this->container->get('doctrine')->getManager();
        $employeeGroupBelongUser = $this->getContainer()->get('app.claim_rule')->getEmployeeGroupBelongToUser($position);
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $rules = $em->createQueryBuilder()
            ->select('limitRule')
            ->from('AppBundle\Entity\LimitRule', 'limitRule')
            ->join('limitRule.limitRuleEmployeeGroups', 'limitRuleEmployeeGroup')
            ->join('limitRuleEmployeeGroup.employeeGroup', 'employeeGroup')
            ->where($expr->eq('limitRule.claimType', ':claimType'))
            ->andWhere($expr->in('employeeGroup.description', ':employeeGroupBelongUser'))
            ->setParameter('employeeGroupBelongUser', $employeeGroupBelongUser)
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
            ->setParameter('company', $this->getClientCompany())
            ->andWhere($expr->in('claimCategory.id', $listCategory));//if $listCategory
        return $qb;
    }

    public function filterTaxRateBycompanyByClaim(Claim $claim)

    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('taxRate')
            ->from('AppBundle\Entity\TaxRate', 'taxRate')
            ->where($expr->eq('taxRate.company', ':company'))
            ->andWhere($expr->eq('taxRate.isLocalDefault', ':localDefault'))
            ->setParameter('company', $this->getClientCompany());
        if ($claim->getClaimType()->getClaimTypeType()->getName() == 'Local') {
            $qb->setParameter('localDefault', true);
        } else {
            $qb->setParameter('localDefault', false);
        }
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

                $formMapper->add('claimAmountConverted', 'number', [
                    'label' => 'Receipt Value after conversion',
                    'required' => false,
                    'attr' => ['readonly' => true]
                ]);

                $formMapper->add('taxAmountConverted', 'number', [
                    'label' => 'Tax Value after Conversion',
                    'required' => false,
                    'attr' => ['readonly' => true]
                ]);
            }
            $formMapper->add('claimAmount', 'number', ['label' => 'Receipt Amount']);
            $formMapper->add('description', 'text', ['label' => 'Claim Description']);
            $formMapper->add('receiptDate', 'date', ['widget' => 'single_text', 'format' => 'MM/dd/yyyy']);
            $formMapper->add('taxRate', 'sonata_type_model', array(
                'property' => 'code',
                'query' => $this->filterTaxRateBycompanyByClaim($subject),
                'placeholder' => 'None',
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
            $formMapper->add('taxAmount', 'number', [
                'label' => 'Tax Amount',
                'required' => false,
                'attr' => ['readonly' => true]
            ]);
        }
    }


    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $request = $this->getRequest();
        $type = $request->get('type');
        switch ($type) {
            case 'checking-each-position':
                $datagridMapper->add('claim_period', 'doctrine_orm_callback', array(
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!$value['value']) {
                            return;
                        }
                        $dateFilter = new  \DateTime($value['value']);
                        $expr = new Expr();
                        $queryBuilder->andWhere($expr->eq($alias . '.periodFrom', ':periodFrom'));
                        $queryBuilder->setParameter('periodFrom', $dateFilter->format('Y-m-d'));
                        return true;
                    },
                    'field_type' => 'choice',
                    'field_options' => ['attr' => ['placeholder' => 'Name, Email, Employee No, NRIC/Fin'],
                        'choices' => $this->getContainer()->get('app.claim_rule')->getListClaimPeriodForFilterChecker()
                    ],
                    'advanced_filter' => false,

                ));
                break;
            case 'approving-each-position':
                $datagridMapper->add('claim_period', 'doctrine_orm_callback', array(
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!$value['value']) {
                            return;
                        }
                        $dateFilter = new  \DateTime($value['value']);
                        $expr = new Expr();
                        $queryBuilder->andWhere($expr->eq($alias . '.periodFrom', ':periodFrom'));
                        $queryBuilder->setParameter('periodFrom', $dateFilter->format('Y-m-d'));
                        return true;
                    },
                    'field_type' => 'choice',
                    'field_options' => ['attr' => ['placeholder' => 'Name, Email, Employee No, NRIC/Fin'],
                        'choices' => $this->getContainer()->get('app.claim_rule')->getListClaimPeriodForFilterApprover()
                    ],
                    'advanced_filter' => false,

                ));
                break;
            case null:
                $datagridMapper->add('claim_period', 'doctrine_orm_callback', array(
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!$value['value']) {
                            return;
                        }
                        $dateFilter = new  \DateTime($value['value']);
                        $expr = new Expr();
                        $queryBuilder->andWhere($expr->eq($alias . '.periodFrom', ':periodFrom'));
                        $queryBuilder->setParameter('periodFrom', $dateFilter->format('Y-m-d'));
                        return true;
                    },
                    'field_type' => 'choice',
                    'field_options' => ['attr' => ['placeholder' => 'Name, Email, Employee No, NRIC/Fin'],
                        'choices' => $this->getContainer()->get('app.claim_rule')->getListClaimPeriodForFilterEmployee()
                    ],
                    'advanced_filter' => false,

                ));
                break;
            default:

        }
    }

    protected
    function configureListFields(ListMapper $listMapper)
    {
        $request = $this->getRequest();
        $type = $request->get('type');
        switch ($type) {
            case 'checking-each-position':
            case 'approving-each-position':
                $listMapper
                    ->add('position.employeeNo', null, ['label' => 'Employee No'])
                    ->add('position.firstName', null, ['label' => 'Name'])
                    ->add('position.employeeGroup.costCentre.code', null, ['label' => 'Cost Centre'])
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
                                'template' => 'AppBundle:SonataAdmin/CustomActions:_list-action-checker_approver-view-claim.html.twig'
                            ),
                        )
                    ));
                break;
            default:

                $listMapper
                    ->add('claimType.code', null, ['label' => 'Claim Type'])
                    ->add('claimCategory.code', null, ['label' => 'Claim Category'])
                    ->add('status', null, ['label' => 'Status'])
                    ->add('claimAmount', null, ['label' => 'Amount'])
                    ->add('a', 'debug', ['label' => 'DEBUG'])
                    ->add('_action', null, array(
                        'actions' => array(
                            'delete' => array(
                                'template' => 'AppBundle:SonataAdmin/CustomActions:_list-action-employee-delete-claim.html.twig'
                            ),
                            'show' => array(),
                            'edit' => array(
                                'template' => 'AppBundle:SonataAdmin/CustomActions:_list-action-employee-edit-claim.html.twig'
                            ),
                        )
                    ));
        }

    }


    protected
    function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('uploadImage', $this->getRouterIdParameter() . '/upload-image-claim');
        $collection->add('deleteImage', $this->getRouterIdParameter() . '/{mediaId}/delete-image-claim');
        $collection->add('checkerApprove', $this->getRouterIdParameter() . '/checker-approve');
        $collection->add('checkerReject', $this->getRouterIdParameter() . '/checker-reject');
        $collection->add('firstPageCreateClaim', 'create-claim');
        $collection->add('listOptionClaim', 'list-claim');
        $collection->add('listUserSubmissionFor', 'list-user-submission-for');
        $collection->add('submitDraftClaims', 'submit-draft-claims');
    }

    protected
    function configureBatchActions($actions)
    {
        return [];
    }

    /**
     * @param ShowMapper $show
     */
    protected
    function configureShowFields(ShowMapper $show)
    {
        $claim = $this->getSubject();
        $request = $this->getRequest();
        $type = $request->get('type');
        switch ($type) {
            case 'checker-view-claim':
                $show->tab('Claim Details');
                $show->with('Claim Details', array('class' => 'col-md-6'));
                $show->add('description', 'text', ['label' => 'Description']);
                $show->add('claimType.code', 'text', ['label' => 'Claim Type']);
                $show->add('claimCategory.code', 'text', ['label' => 'Claim Category']);
                $show->add('3', 'show_claim_limit', ['label' => 'Claim Limit']);
                $show->add('claimAmount', 'show_currency', ['label' => 'Amount', 'currency' => 'USD']);
                $show->add('taxRate.code', 'show_tax_code', ['label' => 'Tax Code']);
                $show->add('taxAmount', null, ['label' => 'Tax Amount']);
                if ($claim->getCurrencyExchange()) {
                    $show->add('currencyExchange.code', null, ['label' => 'Currency']);
                    $show->add('exRate', null, ['label' => 'Ex Rate']);
                    $show->add('claimAmountConverted', null, ['label' => 'Receipt Value after conversion']);
                    $show->add('taxAmountConverted', null, ['label' => 'Tax Value after Conversion']);
                }
                $show->add('status', 'text', ['label' => 'Status']);
                $show->add('receiptDate', 'date', ['label' => 'Receipt Date', 'format' => 'd M Y']);
                $show->add('submissionRemarks', null, ['label' => 'Claimant Submission Remarks']);
                $show->end();
                $show->with('Claim Images', array('class' => 'col-md-6'));
                $show->add('claimMedias', 'show_image', ['label' => 'Claim Images']);
                $show->end();
                $show->end();
                $show->tab('Submission / Employment Details');
                $show->with('Submission Details', array('class' => 'col-md-6'));
                $show->add('createdBy', 'show_claim_created_by', ['label' => 'Submitted By']);
                $show->add('createdAt', null, ['label' => 'Date Submitted', 'format' => 'd M Y h:i A']);
                $show->add('position.firstName', null, ['label' => 'Claimant First Name']);
                $show->add('position.lastName', null, ['label' => 'Claimant Last Name']);
                $show->add('position.employeeNo', null, ['label' => 'Employee No.']);
                $show->add('position.contactNumber', null, ['label' => 'Contact No.']);
                $show->end();
                $show->with('Employment Details', array('class' => 'col-md-6'));
                $show->add('position.company.name', null, ['label' => 'Company']);
                $show->add('position.costCentre.code', null, ['label' => 'Cost Centre']);
                $show->add('position.department.code', null, ['label' => 'Department']);
                $show->add('position.employeeType.code', null, ['label' => 'Employee Type']);
                $show->add('position.employmentType.code', null, ['label' => 'Employment Type']);
                $show->end();
                $show->end();
                break;
            case 'approving-view-claim':
                $show->tab('Claim Details');
                $show->with('Claim Details', array('class' => 'col-md-6'));
                $show->add('description', 'text', ['label' => 'Description']);
                $show->add('claimType.code', 'text', ['label' => 'Claim Type']);
                $show->add('claimCategory.code', 'text', ['label' => 'Claim Category']);
                $show->add('3', 'show_claim_limit', ['label' => 'Claim Limit']);
                $show->add('claimAmount', 'show_currency', ['label' => 'Amount', 'currency' => 'USD']);
                $show->add('taxRate.code', 'show_tax_code', ['label' => 'Tax Code']);
                $show->add('taxAmount', null, ['label' => 'Tax Amount']);
                if ($claim->getCurrencyExchange()) {
                    $show->add('currencyExchange.code', null, ['label' => 'Currency']);
                    $show->add('exRate', null, ['label' => 'Ex Rate']);
                    $show->add('claimAmountConverted', null, ['label' => 'Receipt Value after conversion']);
                    $show->add('taxAmountConverted', null, ['label' => 'Tax Value after Conversion']);
                }
                $show->add('status', 'text', ['label' => 'Status']);
                $show->add('receiptDate', 'date', ['label' => 'Receipt Date', 'format' => 'd M Y']);
                $show->add('submissionRemarks', null, ['label' => 'Claimant Submission Remarks']);
                $show->end();
                $show->with('Claim Images', array('class' => 'col-md-6'));
                $show->add('claimMedias', 'show_image', ['label' => 'Claim Images']);
                $show->end();
                $show->with('Checker Remarks', array('class' => 'col-md-12'));
                $show->add('checkerRemark', 'show_checker_remark');
                $show->end();
                $show->end();
                $show->tab('Submission / Employment Details');
                $show->with('Submission Details', array('class' => 'col-md-6'));
                $show->add('createdBy', 'show_claim_created_by', ['label' => 'Submitted By']);
                $show->add('createdAt', null, ['label' => 'Date Submitted', 'format' => 'd M Y h:i A']);
                $show->add('position.firstName', null, ['label' => 'Claimant First Name']);
                $show->add('position.lastName', null, ['label' => 'Claimant Last Name']);
                $show->add('position.employeeNo', null, ['label' => 'Employee No.']);
                $show->add('position.contactNumber', null, ['label' => 'Contact No.']);
                $show->end();
                $show->with('Employment Details', array('class' => 'col-md-6'));
                $show->add('position.company.name', null, ['label' => 'Company']);
                $show->add('position.costCentre.code', null, ['label' => 'Cost Centre']);
                $show->add('position.department.code', null, ['label' => 'Department']);
                $show->add('position.employeeType.code', null, ['label' => 'Employee Type']);
                $show->add('position.employmentType.code', null, ['label' => 'Employment Type']);
                $show->end();
                $show->end();
                break;
            case 'employee-preview-claim':
                $show->with('Claim Images', array('class' => 'col-md-6'));
                $show->add('claimMedias', 'show_image', ['label' => 'Claim Images']);
                $show->end();
                $show->with('Claim Details', array('class' => 'col-md-6'));
                $show->add('description', 'text', ['label' => 'Description']);
                $show->add('claimType.code', 'text', ['label' => 'Claim Type']);
                $show->add('claimCategory.code', 'text', ['label' => 'Claim Category']);
                $show->add('3', 'show_claim_limit', ['label' => 'Claim Limit']);
                $show->add('claimAmount', 'show_currency', ['label' => 'Amount', 'currency' => 'USD']);
                $show->add('taxRate.code', 'show_tax_code', ['label' => 'Tax Code']);
                $show->add('taxAmount', null, ['label' => 'Tax Amount']);
                if ($claim->getCurrencyExchange()) {
                    $show->add('currencyExchange.code', null, ['label' => 'Currency']);
                    $show->add('exRate', null, ['label' => 'Ex Rate']);
                    $show->add('claimAmountConverted', null, ['label' => 'Receipt Value after conversion']);
                    $show->add('taxAmountConverted', null, ['label' => 'Tax Value after Conversion']);
                }
                $show->add('status', 'text', ['label' => 'Status']);
                $show->add('receiptDate', 'date', ['label' => 'Receipt Date', 'format' => 'd M Y']);
                $show->end();
                break;
            default:
                $show->with('Claim Images', array('class' => 'col-md-6'));
                $show->add('claimMedias', 'show_image', ['label' => 'Claim Images']);
                $show->end();
                $show->with('Claim Details', array('class' => 'col-md-6'));
                $show->add('description', 'text', ['label' => 'Description']);
                $show->add('claimType.code', 'text', ['label' => 'Claim Type']);
                $show->add('claimCategory.code', 'text', ['label' => 'Claim Category']);
                $show->add('3', 'show_claim_limit', ['label' => 'Claim Limit']);
                $show->add('claimAmount', 'show_currency', ['label' => 'Amount', 'currency' => 'USD']);
                $show->add('taxRate.code', 'show_tax_code', ['label' => 'Tax Code']);
                $show->add('taxAmount', null, ['label' => 'Tax Amount']);
                if ($claim->getCurrencyExchange()) {
                    $show->add('currencyExchange.code', null, ['label' => 'Currency']);
                    $show->add('exRate', null, ['label' => 'Ex Rate']);
                    $show->add('claimAmountConverted', null, ['label' => 'Receipt Value after conversion']);
                    $show->add('taxAmountConverted', null, ['label' => 'Tax Value after Conversion']);
                }
                $show->add('status', 'text', ['label' => 'Status']);
                $show->add('receiptDate', 'date', ['label' => 'Receipt Date', 'format' => 'd M Y']);
                $show->add('submissionRemarks', null, ['label' => 'Claimant Submission Remarks']);
                $show->end();
                $show->with('Approver', array('class' => 'col-md-6'));
                $show->add('approver', 'show_approver', ['label' => 'Company']);
                $show->end();
                $show->with('Checker', array('class' => 'col-md-6'));
                $show->add('checker', 'show_checker', ['label' => 'Company']);
                $show->end();

                break;

        }

    }

    public
    function getNewInstance()
    {
        $object = parent::getNewInstance();
        $object->setClaimType($this->getContainer()->get('app.claim_rule')->getClaimTypeDefault());
        return $object;
    }

    public
    function preUpdate($claim)
    {
        $position = $this->getPosition();
        if ($position->getId() == $claim->getPosition()->getId()) {
            //employee update for claim
            $result = $this->getContainer()->get('app.claim_rule')->assignClaimToSpecificApprover($claim, $position);
            $claim->setApproverEmployee($result['approverEmployee']);
            $claim->setApproverBackupEmployee($result['approverBackupEmployee']);
            if ($claim->getCurrencyExchange()) {
                $claim->setExRate($this->getContainer()->get('app.claim_rule')->getExRate($claim->getCurrencyExchange()->getId(),$claim->getReceiptDate()));
            } else {
                $claim->setClaimAmountConverted($claim->getClaimAmount());
                $claim->setTaxAmountConverted($claim->getTaxAmount());
            }
        } else {
            //approver or checker update claim

        }
        parent::preUpdate($claim); // TODO: Change the autogenerated stub
    }

    public
    function prePersist($claim)
    {
        $position = $this->getPosition();
        //must just save when create not update (will conflict when approver or checker update claim)
        $claim->setChecker($this->getContainer()->get('app.claim_rule')->getChecker($position));
        $claim->setApprover($this->getContainer()->get('app.claim_rule')->getApprover($position));
        $claim->setPosition($position);
        $claim->setCreatedBy($this->getUser()->getLoginWithPosition());
        $claim->setReceiptDate(new \DateTime());
        parent::prePersist($claim); // TODO: Change the autogenerated stub
    }

    public
    function toString($object)
    {
        return $object instanceof Claim
            ? $object->getId()
            : 'Claim'; // shown in the breadcrumb on the create view
    }


}