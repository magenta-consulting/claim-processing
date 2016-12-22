<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Claim;
use AppBundle\Entity\ClaimMedia;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Inflector\Inflector;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\LockException;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Sonata\AdminBundle\Util\AdminObjectAclData;
use Sonata\AdminBundle\Util\AdminObjectAclManipulator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use AppBundle\Entity\ApproverHistory;
use AppBundle\Form\UserPasswordType;

class PositionController extends Controller
{

    /**
     * @param ProxyQueryInterface $selectedModelQuery
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function batchActionProceed(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    {
        $em = $this->getDoctrine()->getManager();
        $modelManager = $this->admin->getModelManager();


        $selectedModels = $selectedModelQuery->execute();

        // do the merge work here

        $currentPeriod = $this->get('app.claim_rule')->getCurrentClaimPeriod('from');
        $filter = $this->admin->getFilterParameters();
        if (isset($filter['claim_period'])) {
            $from = $filter['claim_period']['value'];
        } else {
            $from = $currentPeriod->format('Y-m-d');
        }

        try {
            foreach ($selectedModels as $position) {
                foreach ($position->getClaims() as $claim) {
                    if ($claim->getStatus() == Claim::STATUS_APPROVER_APPROVED) {
                        if ($claim->getPeriodFrom()->format('Y-m-d') == $from || $from == 'all') {
                            $claim->setStatus(Claim::STATUS_PROCESSED);
                            $claim->setProcessedDate(new \DateTime());
                            $modelManager->update($claim);
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', 'flash_batch_merge_error');

            return new RedirectResponse(
                $this->admin->generateUrl('list', array('type' => 'hr', 'filter' => $this->admin->getFilterParameters()))
            );
        }

        $this->addFlash('sonata_flash_success', 'Process Claims Successfully');

        return new RedirectResponse(
            $this->generateUrl('admin_app_claim_excelReport')
        );
    }

    /**
     * @param ProxyQueryInterface $selectedModelQuery
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function batchActionApprove(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    {
        $em = $this->getDoctrine()->getManager();
        $modelManager = $this->admin->getModelManager();


        $selectedModels = $selectedModelQuery->execute();
        $positionLogin = $this->getUser()->getLoginWithPosition();
        // do the merge work here

        $currentPeriod = $this->get('app.claim_rule')->getCurrentClaimPeriod('from');
        $filter = $this->admin->getFilterParameters();
        if (isset($filter['claim_period'])) {
            $from = $filter['claim_period']['value'];
        } else {
            $from = $currentPeriod->format('Y-m-d');
        }
        try {
            foreach ($selectedModels as $position) {
                foreach ($position->getClaims() as $claim) {
                    if ($claim->getStatus() == Claim::STATUS_CHECKER_APPROVED) {
                        if ($claim->getPeriodFrom()->format('Y-m-d') == $from || $from == 'all') {
                            $claim->setStatus(Claim::STATUS_APPROVER_APPROVED);
                            $claim->setApproverUpdatedAt(new \DateTime());
                            $modelManager->update($claim);


                            $history = new ApproverHistory();
                            $history->setClaim($claim);
                            $history->setPosition($claim->getPosition());
                            $history->setApproverPosition($positionLogin);
                            $history->setPeriodFrom($claim->getPeriodFrom());
                            $history->setPeriodTo($claim->getPeriodTo());
                            $history->setStatus(Claim::STATUS_APPROVER_APPROVED);
                            $em->persist($history);
                            $em->flush();
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', 'flash_batch_merge_error');

            return new RedirectResponse(
                $this->admin->generateUrl('list', array('type' => 'approving', 'filter' => $this->admin->getFilterParameters()))
            );
        }

        $this->addFlash('sonata_flash_success', 'Approve Claims Successfully');

        return new RedirectResponse(
            $this->generateUrl('admin_app_position_list', ['type' => 'approving', 'filter' => $this->admin->getFilterParameters()])
        );
    }

    /**
     * Batch action.
     *
     * @param Request $request
     *
     * @return Response|RedirectResponse
     *
     * @throws NotFoundHttpException If the HTTP method is not POST
     * @throws \RuntimeException     If the batch action is not defined
     */
    public function batchAction()
    {
        $request = $this->getRequest();
        $restMethod = $this->getRestMethod();

        if ('POST' !== $restMethod) {
            throw $this->createNotFoundException(sprintf('Invalid request type "%s", POST expected', $restMethod));
        }

        // check the csrf token
        $this->validateCsrfToken('sonata.batch');

        $confirmation = $request->get('confirmation', false);

        if ($data = json_decode($request->get('data'), true)) {
            $action = $data['action'];
            $idx = $data['idx'];
            $allElements = $data['all_elements'];
            $request->request->replace(array_merge($request->request->all(), $data));
        } else {
            $request->request->set('idx', $request->get('idx', array()));
            $request->request->set('all_elements', $request->get('all_elements', false));

            $action = $request->get('action');
            $idx = $request->get('idx');
            $allElements = $request->get('all_elements');
            $data = $request->request->all();

            unset($data['_sonata_csrf_token']);
        }

        // NEXT_MAJOR: Remove reflection check.
        $reflector = new \ReflectionMethod($this->admin, 'getBatchActions');
        if ($reflector->getDeclaringClass()->getName() === get_class($this->admin)) {
            @trigger_error('Override Sonata\AdminBundle\Admin\AbstractAdmin::getBatchActions method'
                . ' is deprecated since version 3.2.'
                . ' Use Sonata\AdminBundle\Admin\AbstractAdmin::configureBatchActions instead.'
                . ' The method will be final in 4.0.', E_USER_DEPRECATED
            );
        }
        $batchActions = $this->admin->getBatchActions();
        if (!array_key_exists($action, $batchActions)) {
            throw new \RuntimeException(sprintf('The `%s` batch action is not defined', $action));
        }

        $camelizedAction = Inflector::classify($action);
        $isRelevantAction = sprintf('batchAction%sIsRelevant', $camelizedAction);

        if (method_exists($this, $isRelevantAction)) {
            $nonRelevantMessage = call_user_func(array($this, $isRelevantAction), $idx, $allElements);
        } else {
            $nonRelevantMessage = count($idx) != 0 || $allElements; // at least one item is selected
        }

        if (!$nonRelevantMessage) { // default non relevant message (if false of null)
            $nonRelevantMessage = 'flash_batch_empty';
        }

        $datagrid = $this->admin->getDatagrid();
        $datagrid->buildPager();

        if (true !== $nonRelevantMessage) {
            $this->addFlash('sonata_flash_info', $nonRelevantMessage);

            return new RedirectResponse(
                $this->admin->generateUrl(
                    'list',
                    array('type' => $this->getRequest()->get('type'))
                )
            );
        }

        $askConfirmation = isset($batchActions[$action]['ask_confirmation']) ?
            $batchActions[$action]['ask_confirmation'] :
            true;

        if ($askConfirmation && $confirmation != 'ok') {
            $actionLabel = $batchActions[$action]['label'];
            $batchTranslationDomain = isset($batchActions[$action]['translation_domain']) ?
                $batchActions[$action]['translation_domain'] :
                $this->admin->getTranslationDomain();

            $formView = $datagrid->getForm()->createView();

            return $this->render($this->admin->getTemplate('batch_confirmation'), array(
                'action' => 'list',
                'action_label' => $actionLabel,
                'batch_translation_domain' => $batchTranslationDomain,
                'datagrid' => $datagrid,
                'form' => $formView,
                'data' => $data,
                'csrf_token' => $this->getCsrfToken('sonata.batch'),
            ), null);
        }

        // execute the action, batchActionXxxxx
        $finalAction = sprintf('batchAction%s', $camelizedAction);
        if (!is_callable(array($this, $finalAction))) {
            throw new \RuntimeException(sprintf('A `%s::%s` method must be callable', get_class($this), $finalAction));
        }

        $query = $datagrid->getQuery();

        $query->setFirstResult(null);
        $query->setMaxResults(null);

        $this->admin->preBatchAction($action, $query, $idx, $allElements);

        if (count($idx) > 0) {
            $this->admin->getModelManager()->addIdentifiersToQuery($this->admin->getClass(), $query, $idx);
        } elseif (!$allElements) {
            $query = null;
        }

        return call_user_func(array($this, $finalAction), $query);
    }

    public function changePasswordAction(Request $request)
    {
        $form = $this->createForm(UserPasswordType::class);
        $userManager = $this->get('fos_user.user_manager');
        $user = $this->getUser();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $user->setPlainPassword($form->get('plainPassword')->getData());
                $userManager->updateUser($user);
                $this->addFlash('message', 'Update account successfully.');
            } else {
                $this->addFlash('error', 'Fail to update.');
            }
        }
        return $this->render('@App/SonataAdmin/Position/change_password.html.twig', ['form' => $form->createView()]);
    }
}