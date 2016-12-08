<?php
namespace AppBundle\Admin;

use AppBundle\Entity\ApproverHistory;
use AppBundle\Entity\Branch;
use AppBundle\Entity\CheckerHistory;
use AppBundle\Entity\CostCentre;
use AppBundle\Entity\Region;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use Doctrine\ORM\Query\Expr;

class ApproverHistoryAdmin extends BaseAdmin
{


    protected
    function configureBatchActions($actions)
    {
        return [];
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('claim_period', 'doctrine_orm_callback', array(
            'callback' => function ($queryBuilder, $alias, $field, $value) {
                if ($value['value'] === 'all') {
                    return;
                } else {
                    $dateFilter = new  \DateTime($value['value']);
                }
                $expr = new Expr();
                $queryBuilder->andWhere($expr->eq($alias . '.periodFrom', ':periodFrom'));
                $queryBuilder->setParameter('periodFrom', $dateFilter->format('Y-m-d'));
                return true;
            },
            'field_type' => 'choice',
            'field_options' => ['attr' => ['placeholder' => 'Name, Email, Employee No, NRIC/Fin'],
                'choices' => $this->getContainer()->get('app.approver_rule')->getListClaimPeriodForFilterApproverHistory(),
                'empty_data' => $this->getContainer()->get('app.claim_rule')->getCurrentClaimPeriod('from')->format('Y-m-d'),
            ],
            'advanced_filter' => false,

        ));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('claim.position.employeeNo', null, ['label' => 'Employee No', 'sortable' => false])
            ->add('claim.position.firstName', null, ['label' => 'Name', 'sortable' => false])
            ->add('claim.position.employeeGroup.costCentre.code', null, ['label' => 'Cost Centre', 'sortable' => false])
            ->add('claim.claimType.code', null, ['label' => 'Claim Type', 'sortable' => false])
            ->add('claim.claimCategory.code', null, ['label' => 'Claim Category', 'sortable' => false])
            ->add('periodFrom', 'date', ['label' => 'Period From', 'format' => 'd M Y', 'sortable' => false])
            ->add('periodTo', null, ['label' => 'Period To', 'format' => 'd M Y', 'sortable' => false])
            ->add('status', null, ['label' => 'Status', 'sortable' => false])
            ->add('claim.createdAt', null, ['label' => 'Submission Date', 'format' => 'd M Y', 'sortable' => false])
            ->add('claim.claimAmountConverted', null, ['label' => 'Amount', 'sortable' => false])
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(
                        'template' => 'AppBundle:SonataAdmin/CustomActions:_list-action-checker_approver_hr-view-claim.html.twig'
                    ),
                )
            ));
    }

    public function toString($object)
    {
        return $object instanceof ApproverHistory
            ? 'Approver History'
            : 'Approver History'; // shown in the breadcrumb on the create view
    }



}