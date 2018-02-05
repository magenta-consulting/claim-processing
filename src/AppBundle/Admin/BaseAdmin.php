<?php
namespace AppBundle\Admin;

use AppBundle\Entity\ApproverHistory;
use AppBundle\Entity\CheckerHistory;
use AppBundle\Entity\Claim;
use AppBundle\Entity\ClaimType;
use AppBundle\Entity\ClaimTypeType;
use AppBundle\Entity\Company;
use AppBundle\Entity\CompanyClaimPolicies;
use AppBundle\Entity\CostCentre;
use AppBundle\Entity\PayCodeType;
use AppBundle\Entity\Position;
use AppBundle\Entity\Region;
use Doctrine\ORM\Query\Expr;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Route\RouteCollection;
use AppBundle\Entity\User;

class BaseAdmin extends AbstractAdmin
{
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
	
	/**
	 * @return ContainerInterface
	 */
    public function getContainer()
    {
        return $this->container;
    }

    public function getUser()
    {
        return $this->getContainer()->get('app.claim_rule')->getUser();
    }

    public function getPosition()
    {
        return $this->getContainer()->get('app.claim_rule')->getPosition();
    }

    public function getCompany()
    {
        //admin will return null
        return $this->getContainer()->get('app.claim_rule')->getCompany();
    }

    public function getClientCompany()
    {
        //admin will return null
        return $this->getContainer()->get('app.claim_rule')->getClientCompany();
    }


    public function isAdmin()
    {
        if ($this->getContainer()->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return true;
        }
        return false;
    }

    public function isCLient()
    {
        if ($this->getContainer()->get('security.authorization_checker')->isGranted('ROLE_CLIENT_ADMIN')) {
            return true;
        }
        return false;
    }

    public function isUser()
    {
        if ($this->getContainer()->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            return true;
        }
        return false;
    }


    public function isHr()
    {
        if ($this->getContainer()->get('security.authorization_checker')->isGranted('ROLE_HR_ADMIN')) {
            return true;
        }
        return false;
    }
    
    public function isAccountant()
    {
        if ($this->getContainer()->get('security.authorization_checker')->isGranted('ROLE_ACCOUNTANT')) {
            return true;
        }
        return false;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
    }

    public function manualUpdate($object)
    {

    }

    /*
    * add company when add new(not effect when update)
    */
    public function prePersist($object)
    {
        $this->manualUpdate($object);
        if ($this->isAdmin()) {
            //admin current only create a client company (parent = null)
            if ($object instanceof Company) {
                $em = $this->getContainer()->get('doctrine')->getManager();
                //add Pay Code Type: By system default always has “Deductions” and “Allowances”
                $payCodeType1 = new PayCodeType();
                $payCodeType1->setName('Deductions');
                $payCodeType1->setOrderSort(1);
                $payCodeType1->setEnabled(true);
                $payCodeType1->setCompany($object);
                $em->persist($payCodeType1);

                $payCodeType2 = new PayCodeType();
                $payCodeType2->setName('Allowances');
                $payCodeType2->setOrderSort(2);
                $payCodeType2->setEnabled(true);
                $payCodeType2->setCompany($object);
                $em->persist($payCodeType2);

                $payCodeType3 = new PayCodeType();
                $payCodeType3->setName('Expense');
                $payCodeType3->setOrderSort(3);
                $payCodeType3->setEnabled(true);
                $payCodeType3->setCompany($object);
                $em->persist($payCodeType3);


                //create claim type
                $claimTypeType1 = new ClaimTypeType();
                $claimTypeType1->setCompany($object);
                $claimTypeType1->setEnabled(true);
                $claimTypeType1->setName('Local');
                $claimTypeType1->setOrderSort(1);
                $em->persist($claimTypeType1);

                $claimTypeType2 = new ClaimTypeType();
                $claimTypeType2->setCompany($object);
                $claimTypeType2->setEnabled(true);
                $claimTypeType2->setName('Overseas');
                $claimTypeType2->setOrderSort(2);
                $em->persist($claimTypeType2);

                $claimType1 = new ClaimType();
                $claimType1->setCompany($object);
                $claimType1->setEnabled(true);
                $claimType1->setClaimTypeType($claimTypeType1);
                $claimType1->setCode('Local Claims');
                $em->persist($claimType1);

                $claimType2 = new ClaimType();
                $claimType2->setCompany($object);
                $claimType2->setEnabled(true);
                $claimType2->setClaimTypeType($claimTypeType2);
                $claimType2->setCode('Overseas Claims');
                $em->persist($claimType2);


                $em->flush();

            }
        }

    }

    public function preUpdate($object)
    {
        $this->manualUpdate($object);
    }


    public function getNewInstance()
    {
        $object = parent::getNewInstance(); // TODO: Change the autogenerated stub
        if ($this->isCLient()) {
            /*because client manage many sub company so when create position ,it belong company is selected not ($this->getCompany() from user login)*/
            if (!$object instanceof Company && !$object instanceof Position) {
                if (method_exists($object, 'setCompany')) {
                    $object->setCompany($this->getCompany());
                }
            } elseif ($object instanceof Company) {
                if ($object->getId() !== $this->getCompany()->getId()) {
                    $object->setParent($this->getCompany());
                }
            }
        }
        if ($this->isUser()) {
            if (method_exists($object, 'setCompany')) {
                $object->setCompany($this->getCompany());
            }
        }
        return $object;
    }

    /*
     * filter by company list
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $class = $this->getClass();
        $company = $this->getCompany();
        $clientCompany = $this->getClientCompany();
        $position = $this->getPosition();
        $expr = new Expr();
        if ($this->isAdmin()) {
            if ($class === 'AppBundle\Entity\Company') {
                $query->andWhere(
                    $expr->isNull($query->getRootAliases()[0] . '.parent')
                );

            }
            if ($this->getClass() == 'AppBundle\Entity\Position' && !$this->getRequest()->isXmlHttpRequest()) {
                $query->andWhere(
                    $expr->eq($query->getRootAliases()[0] . '.thirdParty', ':thirdParty')
                );
                $query->setParameter('thirdParty', false);
            }
        }
        if ($this->isCLient()) {
            if ($class === 'AppBundle\Entity\Company') {
                $query->andWhere(
                    $expr->orX(
                        $expr->eq($query->getRootAliases()[0] . '.parent', ':company'),
                        $expr->eq($query->getRootAliases()[0], ':company')
                    )
                );

                $query->setParameter('company', $company);
            } else {
                if ($this->getClass() !== 'AppBundle\Entity\Position' && property_exists ($this->getClass(),'company')) {
                    //manage infor except position
                    $query->andWhere(
                        $expr->eq($query->getRootAliases()[0] . '.company', ':company')
                    );
                    $query->setParameter('company', $company);
                } elseif ($this->getClass() == 'AppBundle\Entity\Position' && $this->getRequest()->isXmlHttpRequest()) {
                    //when get list user by ajax will get all belong sub company or client company
                    $query->join($query->getRootAliases()[0] . '.company', 'company');
                    $query->andWhere(
                        $expr->orX(
                            $expr->eq('company', ':company'),
                            $expr->eq('company.parent', ':clientCompany')
                        )
                    );
                    $query->setParameter('company', $company);
                    $query->setParameter('clientCompany', $clientCompany);
                } elseif ($this->getClass() == 'AppBundle\Entity\Position' && !$this->getRequest()->isXmlHttpRequest()) {
                    $query->andWhere(
                        $expr->eq($query->getRootAliases()[0] . '.thirdParty', ':thirdParty')
                    );
                    $query->setParameter('thirdParty', false);
                }
                //when get user by comnany the system is automticly get user belong this company by param in url (company)

            }
        }
        if ($this->isHr()) {
            if ($this->getClass() == 'AppBundle\Entity\Position' && !$this->getRequest()->isXmlHttpRequest()) {
                $query->andWhere(
                    $expr->eq($query->getRootAliases()[0] . '.thirdParty', ':thirdParty')
                );
                $query->setParameter('thirdParty', false);
            }
            if ($this->getClass() !== CheckerHistory::class && property_exists ($this->getClass(),'company')) {
                $query->andWhere(
                    $expr->eq($query->getRootAliases()[0] . '.company', ':company')
                );

                $query->setParameter('company', $company);
            }
        }
        if ($this->isUser()) {
            if ($class === 'AppBundle\Entity\CheckerHistory') {
                $request = $this->getRequest();
                $positionId = $request->get('position-id');
                $query->join($query->getRootAliases()[0] . '.position', 'position');
                $query->join($query->getRootAliases()[0] . '.checkerPosition', 'checkerPosition');
                $query->andWhere(
                    $expr->eq('position.id', ':positionId')
                );
                $query->andWhere(
                    $expr->eq('checkerPosition', ':checkerPosition')
                );
                $query->setParameter('positionId', $positionId);
                $query->setParameter('checkerPosition', $this->getPosition());
            }
            if ($class === 'AppBundle\Entity\ApproverHistory') {
                $request = $this->getRequest();
                $positionId = $request->get('position-id');
                $query->join($query->getRootAliases()[0] . '.position', 'position');
                $query->join($query->getRootAliases()[0] . '.approverPosition', 'approverPosition');
                $query->andWhere(
                    $expr->eq('position.id', ':positionId')
                );
                $query->andWhere(
                    $expr->eq('approverPosition', ':approverPosition')
                );
                $query->setParameter('positionId', $positionId);
                $query->setParameter('approverPosition', $this->getPosition());
            }
            if ($class === 'AppBundle\Entity\Claim') {
                $periodFrom = $this->getContainer()->get('app.claim_rule')->getCurrentClaimPeriod('from');
                $periodTo = $this->getContainer()->get('app.claim_rule')->getCurrentClaimPeriod('to');
                $dateFrom = $this->getContainer()->get('app.claim_rule')->getFlexiPeriod('from');
                $dateTo = $this->getContainer()->get('app.claim_rule')->getFlexiPeriod('to');
                $request = $this->getRequest();
                $type = $request->get('type');
                switch ($type) {
                    case 'checking-each-position':
                        $positionId = $request->get('position-id');
                        $query->join($query->getRootAliases()[0] . '.position', 'position');
                        $query->join($query->getRootAliases()[0] . '.checker', 'checker');
                        $query->andWhere(
                            $expr->eq('position.id', ':positionId')
                        );
                        $query->andWhere(
                            $expr->orX(
                                $expr->eq('checker.checker', ':checker'),
                                $expr->eq('checker.backupChecker', ':checker')
                            )
                        );

                        $query->andWhere($expr->eq($query->getRootAliases()[0] . '.status', ':statusPending'));
                        $query->setParameter('statusPending', Claim::STATUS_PENDING);
                        $query->setParameter('positionId', $positionId);
                        $query->setParameter('checker', $this->getPosition());
                        break;

                    case 'approving-each-position':
                        $positionId = $request->get('position-id');
                        $query->join($query->getRootAliases()[0] . '.position', 'position');
                        $query->andWhere(
                            $expr->eq('position.id', ':positionId')
                        );
                        $query->andWhere(
                            $expr->orX(
                                $expr->eq($query->getRootAliases()[0] . '.approverEmployee', ':approverEmployee'),
                                $expr->eq($query->getRootAliases()[0] . '.approverBackupEmployee', ':approverEmployee')
                            )
                        );
                        $query->andWhere($expr->in($query->getRootAliases()[0] . '.status', ':states'));
	                    
	                    $query->setParameter('states', [
		                    Claim::STATUS_CHECKER_APPROVED,
		                    Claim::STATUS_APPROVER_APPROVED_FIRST,
		                    Claim::STATUS_APPROVER_APPROVED_SECOND,
		                    Claim::STATUS_APPROVER_APPROVED_THIRD
	                    ]);
                        
                        
                        
                        $query->setParameter('positionId', $positionId);
                        $query->setParameter('approverEmployee', $this->getPosition());
                        break;
                    case 'hr-each-position':
                    case 'hr-reject-each-position':
                        $positionId = $request->get('position-id');
                        $query->join($query->getRootAliases()[0] . '.position', 'position');
                        $query->andWhere(
                            $expr->eq('position.id', ':positionId')
                        );
                        $query->andWhere(
                            $expr->orX(
                                $expr->eq($query->getRootAliases()[0] . '.status', ':statusApproverApproved')
                            )
                        );
                        $query->setParameter('statusApproverApproved', Claim::STATUS_APPROVER_APPROVED);
                        $query->setParameter('positionId', $positionId);
                        break;
                    case 'hr-report-each-position':
                        $positionId = $request->get('position-id');
                        $query->join($query->getRootAliases()[0] . '.position', 'position');
                        $query->andWhere(
                            $expr->eq('position.id', ':positionId')
                        );
                        $query->andWhere(
                            $expr->orX(
                                $expr->eq($query->getRootAliases()[0] . '.status', ':statusHrApproved')
                            )
                        );
                        $query->setParameter('statusHrApproved', Claim::STATUS_PROCESSED);
                        $query->setParameter('positionId', $positionId);
                        break;
                    case 'flexi':
                        $query->andWhere(
                            $expr->eq($query->getRootAliases()[0] . '.position', ':position')
                        );
                        $query->andWhere(
                            $expr->neq($query->getRootAliases()[0] . '.status', ':status')
                        );
                        $query->andWhere(
                            $expr->eq($query->getRootAliases()[0] . '.flexiClaim', ':flexiClaim')
                        );
                        $query->andWhere($query->getRootAliases()[0] . '.receiptDate >= :dateFrom');
                        $query->andWhere($query->getRootAliases()[0] . '.receiptDate < :dateTo');
                        $query->setParameter('dateFrom', $dateFrom);
                        $query->setParameter('dateTo', $dateTo);
                        $query->setParameter('position', $position);
                        $query->setParameter('status', Claim::STATUS_NOT_USE);
                        $query->setParameter('flexiClaim', true);
                        break;
                    case 'current':
                        $query->andWhere(
                            $expr->eq($query->getRootAliases()[0] . '.position', ':position')
                        );
                        $query->andWhere($expr->orX(
                            $expr->eq($query->getRootAliases()[0] . '.status', ':statusPending'),
                            $expr->in($query->getRootAliases()[0] . '.status', ':states'),
	                        $expr->eq($query->getRootAliases()[0] . '.status', ':statusApproverApprove'),
	                        $expr->eq($query->getRootAliases()[0] . '.status', ':statusApproverApprove1'),
	                        $expr->eq($query->getRootAliases()[0] . '.status', ':statusApproverApprove2')
                        ));
                        $query->andWhere(
                            $expr->eq($query->getRootAliases()[0] . '.periodFrom', ':periodFrom')
                        );
                        $query->andWhere(
                            $expr->eq($query->getRootAliases()[0] . '.periodTo', ':periodTo')
                        );
                        $query->setParameter('periodFrom', $periodFrom->format('Y-m-d'));
                        $query->setParameter('periodTo', $periodTo->format('Y-m-d'));
                        $query->setParameter('position', $position);
                        $query->setParameter('statusPending', Claim::STATUS_PENDING);
                        
	                    $query->setParameter('states', [
		                    Claim::STATUS_CHECKER_APPROVED,
		                    Claim::STATUS_APPROVER_APPROVED_FIRST,
		                    Claim::STATUS_APPROVER_APPROVED_SECOND,
		                    Claim::STATUS_APPROVER_APPROVED_THIRD
	                    ]);
                        
                        $query
	                        ->setParameter('statusApproverApprove', Claim::STATUS_APPROVER_APPROVED)
	                        ->setParameter('statusApproverApprove1', Claim::STATUS_APPROVER_APPROVED_FIRST)
	                        ->setParameter('statusApproverApprove2', Claim::STATUS_APPROVER_APPROVED_SECOND)
                        ;
                        break;
                    case 'draft':
                        $query->andWhere(
                            $expr->eq($query->getRootAliases()[0] . '.position', ':position')
                        );
                        $query->andWhere(
                            $expr->eq($query->getRootAliases()[0] . '.status', ':status')
                        );
                        $query->andWhere(
                            $expr->eq($query->getRootAliases()[0] . '.periodFrom', ':periodFrom')
                        );
                        $query->andWhere(
                            $expr->eq($query->getRootAliases()[0] . '.periodTo', ':periodTo')
                        );
                        $query->setParameter('periodFrom', $periodFrom->format('Y-m-d'));
                        $query->setParameter('periodTo', $periodTo->format('Y-m-d'));
                        $query->setParameter('status', Claim::STATUS_DRAFT);
                        $query->setParameter('position', $position);
                        break;
                    case 'reject':
                        $query->andWhere(
                            $expr->eq($query->getRootAliases()[0] . '.position', ':position')
                        );
                        $query->andWhere(
                            $expr->eq($query->getRootAliases()[0] . '.periodFrom', ':periodFrom')
                        );
                        $query->andWhere(
                            $expr->eq($query->getRootAliases()[0] . '.periodTo', ':periodTo')
                        );
                        $query->andWhere($expr->orX(
                            $expr->eq($query->getRootAliases()[0] . '.status', ':statusCheckerRejected'),
                            $expr->eq($query->getRootAliases()[0] . '.status', ':statusApproverRejected'),
                            $expr->eq($query->getRootAliases()[0] . '.status', ':statusHrRejected')
                        ));
                        $query->setParameter('periodFrom', $periodFrom->format('Y-m-d'));
                        $query->setParameter('periodTo', $periodTo->format('Y-m-d'));
                        $query->setParameter('statusCheckerRejected', Claim::STATUS_CHECKER_REJECTED);
                        $query->setParameter('statusApproverRejected', Claim::STATUS_APPROVER_REJECTED);
                        $query->setParameter('statusHrRejected', Claim::STATUS_HR_REJECTED);
                        $query->setParameter('position', $position);
                        break;
                    default:
                        $query->andWhere(
                            $expr->eq($query->getRootAliases()[0] . '.position', ':position')
                        );
                        $query->andWhere(
                            $expr->neq($query->getRootAliases()[0] . '.status', ':status')
                        );

                        $query->setParameter('status', Claim::STATUS_NOT_USE);
                        $query->setParameter('position', $position);
                }
            }
            if ($class === 'AppBundle\Entity\Position') {
                $request = $this->getRequest();
                $type = $request->get('type');
                switch ($type) {
                    case 'checking':
                        $query->leftJoin($query->getRootAliases()[0] . '.claims', 'claim');
                        $query->leftJoin('claim.checker', 'checker');
                        $query->leftJoin($query->getRootAliases()[0] . '.company', 'company');
                        $query->andWhere(
                            $expr->orX(
                                $expr->eq('company.parent', ':clientCompany'),
                                $expr->eq('company', ':company')
                            )
                        );
                        $query->andWhere(
                            $expr->orX(
                                $expr->eq('checker.checker', ':checker'),
                                $expr->eq('checker.backupChecker', ':checker')
                            )
                        );
                        $query->andWhere($expr->eq('claim.status', ':statusPending'));
                        $query->setParameter('statusPending', Claim::STATUS_PENDING);
                        $query->setParameter('checker', $position);
                        $query->setParameter('company', $company);
                        $query->setParameter('clientCompany', $clientCompany);
                        break;
                    case 'checker-history':
                        $query->leftJoin($query->getRootAliases()[0] . '.checkingHistories', 'checkingHistory');
                        $query->andWhere($expr->eq('checkingHistory.checkerPosition', ':checkerPosition'));
                        $query->setParameter('checkerPosition', $position);
                        break;
                    case 'approving':
                        $query->leftJoin($query->getRootAliases()[0] . '.claims', 'claim');
                        $query->leftJoin($query->getRootAliases()[0] . '.company', 'company');
                        $query->andWhere(
                            $expr->orX(
                                $expr->eq('company.parent', ':clientCompany'),
                                $expr->eq('company', ':company')
                            )
                        );
                        $query->andWhere(
                            $expr->orX(
                                $expr->eq('claim.approverEmployee', ':position'),
                                $expr->eq('claim.approverBackupEmployee', ':position')
                            )
                        );
	
	                    $query->andWhere($expr->in('claim.status', ':states'));
	                    $query->setParameter('states', [
		                    Claim::STATUS_CHECKER_APPROVED,
		                    Claim::STATUS_APPROVER_APPROVED_FIRST,
		                    Claim::STATUS_APPROVER_APPROVED_SECOND,
		                    Claim::STATUS_APPROVER_APPROVED_THIRD
	                    ]);
                        
                        
                        
                        
                        $query->setParameter('position', $position);
                        $query->setParameter('company', $company);
                        $query->setParameter('clientCompany', $clientCompany);
                        break;
                    case 'approver-history':
                        $query->leftJoin($query->getRootAliases()[0] . '.approverHistories', 'approverHistory');
                        $query->andWhere($expr->eq('approverHistory.approverPosition', ':approverPosition'));
                        $query->setParameter('approverPosition', $position);
                        break;
                    case 'hr':
                    case 'hr-reject':
                        $query->leftJoin($query->getRootAliases()[0] . '.claims', 'claim');
                        $query->leftJoin($query->getRootAliases()[0] . '.company', 'company');
                        $query->andWhere(
                            $expr->eq('company', ':company')
                        );
                        $query->andWhere($expr->eq('claim.status', ':statusCheckerApproved'));
                        $query->setParameter('statusCheckerApproved', Claim::STATUS_APPROVER_APPROVED);
                        $query->setParameter('company', $company);
                        break;
                }
            }
        }

        return $query;
    }


    public function filterCompanyBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('company')
            ->from('AppBundle\Entity\Company', 'company')
            ->where(
                $expr->orX(
                    $expr->eq('company.parent', ':company'),
                    $expr->eq('company', ':company')
                )
            )
            ->setParameter('company', $this->getClientCompany());
        return $qb;
    }

    public function filterCostCentreBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('costCentre')
            ->from('AppBundle\Entity\CostCentre', 'costCentre')
            ->where($expr->eq('costCentre.company', ':company'))
            ->andWhere($expr->eq('costCentre.enabled', true))
            ->setParameter('company', $this->getClientCompany());
        return $qb;
    }

    public function filterRegionBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('region')
            ->from('AppBundle\Entity\Region', 'region')
            ->where($expr->eq('region.company', ':company'))
            ->andWhere($expr->eq('region.enabled', true))
            ->setParameter('company', $this->getClientCompany());
        return $qb;
    }

    public function filterBranchBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('branch')
            ->from('AppBundle\Entity\Branch', 'branch')
            ->where($expr->eq('branch.company', ':company'))
            ->andWhere($expr->eq('branch.enabled', true))
            ->setParameter('company', $this->getClientCompany());
        return $qb;
    }

    public function filterDepartmentBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('department')
            ->from('AppBundle\Entity\Department', 'department')
            ->where($expr->eq('department.company', ':company'))
            ->andWhere($expr->eq('department.enabled', true))
            ->setParameter('company', $this->getClientCompany());
        return $qb;
    }

    public function filterSectionBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('section')
            ->from('AppBundle\Entity\Section', 'section')
            ->where($expr->eq('section.company', ':company'))
            ->andWhere($expr->eq('section.enabled', true))
            ->setParameter('company', $this->getClientCompany());
        return $qb;
    }

    public function filterEmployeeTypeBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('employeeType')
            ->from('AppBundle\Entity\EmployeeType', 'employeeType')
            ->where($expr->eq('employeeType.company', ':company'))
            ->setParameter('company', $this->getClientCompany());
        return $qb;
    }

    public function filterClaimTypeBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('claimType')
            ->from('AppBundle\Entity\ClaimType', 'claimType')
            ->where($expr->eq('claimType.company', ':company'))
            ->andWhere($expr->eq('claimType.enabled', true))
            ->setParameter('company', $this->getClientCompany());
        return $qb;
    }


    public function filterClaimCategoryBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('claimCategory')
            ->from('AppBundle\Entity\ClaimCategory', 'claimCategory')
            ->where($expr->eq('claimCategory.company', ':company'))
            ->setParameter('company', $this->getClientCompany());
        return $qb;
    }

    public function filterTaxRateBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('taxRate')
            ->from('AppBundle\Entity\TaxRate', 'taxRate')
            ->where($expr->eq('taxRate.company', ':company'))
            ->setParameter('company', $this->getClientCompany());
        return $qb;
    }


    public function filterPayCostBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('payCode')
            ->from('AppBundle\Entity\PayCode', 'payCode')
            ->where($expr->eq('payCode.company', ':company'))
            ->andWhere($expr->eq('payCode.enabled', true))
            ->setParameter('company', $this->getClientCompany());
        return $qb;
    }

    public function filterClaimTypeTypeBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('claimTypeType')
            ->from('AppBundle\Entity\ClaimTypeType', 'claimTypeType')
            ->where($expr->eq('claimTypeType.company', ':company'))
            ->andWhere($expr->eq('claimTypeType.enabled', true))
            ->setParameter('company', $this->getClientCompany());
        return $qb;
    }

    public function filterPayCodeTypeBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('payCodeType')
            ->from('AppBundle\Entity\PayCodeType', 'payCodeType')
            ->where($expr->eq('payCodeType.company', ':company'))
            ->andWhere($expr->eq('payCodeType.enabled', true))
            ->setParameter('company', $this->getClientCompany());
        return $qb;
    }

    public function filterEmploymentTypeBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('employmentType')
            ->from('AppBundle\Entity\EmploymentType', 'employmentType')
            ->where($expr->eq('employmentType.company', ':company'))
            ->setParameter('company', $this->getClientCompany());
        return $qb;
    }

    public function filterCurrencyExchangeBycompany()
    {
        $em = $this->container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $expr = new Expr();
        $qb->select('currencyExchange')
            ->from('AppBundle\Entity\CurrencyExchange', 'currencyExchange')
            ->where($expr->eq('currencyExchange.company', ':company'))
            ->setParameter('company', $this->getClientCompany());
        return $qb;
    }


}