<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Claim;
use AppBundle\Entity\ClaimMedia;
use AppBundle\Entity\Position;
use Application\Sonata\MediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ClaimController extends Controller
{


    public function payMasterAction()
    {
        $from = 'none';
        $filter = $this->getRequest()->get('filter');
        if (isset($filter['claim_period'])) {
            $from = $filter['claim_period']['value'];
        }
        $positions = $this->get('app.hr_rule')->getDataForPayMaster($from);

        return $this->render('@App/SonataAdmin/Claim/pay_master.html.twig', ['positions' => $positions, 'from' => $from]);
    }

    public function payMasterExportAction($from)
    {
        $positions = $this->get('app.hr_rule')->getDataForPayMaster($from);
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        //set some static value
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A1', "Emp No.");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B1', 'Name');
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C1', 'Company');
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D1', 'Cost Centre Code');
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E1', "Employment Type");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F1', "Employee Type");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G1', "Region");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H1', "Branch");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I1', "Section");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('J1', "Date/Time Processed");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('K1', "Total Claim Amount");

        foreach ($positions as $k => $position) {
            $num = $k + 2;
            $totalAmount = $this->get('app.hr_rule')->getTotalAmountClaimEachEmployeeForHrReport($position);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A' . $num, $position->getEmployeeNo());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B' . $num, $position->getFirstName() . ' ' . $position->getLastName());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C' . $num, $position->getCompany()->getName());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D' . $num, $position->getCostCentre() ? $position->getCostCentre()->getCode() : 'N/A');
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E' . $num, $position->getEmploymentType() ? $position->getEmploymentType()->getCode() : 'N/A');
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F' . $num, $position->getEmployeeType() ? $position->getEmployeeType()->getCode() : 'N/A');
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G' . $num, $position->getRegion() ? $position->getRegion()->getCode() : 'N/A');
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H' . $num, $position->getBranch() ? $position->getBranch()->getCode() : 'N/A');
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I' . $num, $position->getSection() ? $position->getSection()->getCode() : 'N/A');
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('J' . $num, 'N/A');
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('K' . $num, number_format($totalAmount, 2, '.', ','));
        }
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);
        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'pay-master.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;
    }

    public function excelReportAction()
    {
        $from = 'none';
        $filter = $this->getRequest()->get('filter');
        if (isset($filter['claim_period'])) {
            $from = $filter['claim_period']['value'];
        }
        $claims = $this->get('app.hr_rule')->getDataForExcelReport($from);
        return $this->render('@App/SonataAdmin/Claim/excel_report.html.twig', ['claims' => $claims, 'from' => $from]);
    }

    public function excelReportExportAction($from)
    {
        $claims = $this->get('app.hr_rule')->getDataForExcelReport($from);
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        //set some static value
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A1', "Emp No.");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B1', 'Name');
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C1', 'Company');
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D1', 'Cost Centre Code');
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E1', "Employment Type");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F1', "Employee Type");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G1', "Region");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H1', "Branch");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I1', "Section");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('J1', "Date/Time Processed");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('K1', "Claim Type");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('L1', "Claim Category");
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue('M1', "Claim Amount");

        foreach ($claims as $k => $claim) {
            $position = $claim->getPosition();
            $num = $k + 2;
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('A' . $num, $position->getEmployeeNo());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('B' . $num, $position->getFirstName() . ' ' . $position->getLastName());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('C' . $num, $position->getCompany()->getName());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('D' . $num, $position->getCostCentre() ? $position->getCostCentre()->getCode() : 'N/A');
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('E' . $num, $position->getEmploymentType() ? $position->getEmploymentType()->getCode() : 'N/A');
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('F' . $num, $position->getEmployeeType() ? $position->getEmployeeType()->getCode() : 'N/A');
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('G' . $num, $position->getRegion() ? $position->getRegion()->getCode() : 'N/A');
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('H' . $num, $position->getBranch() ? $position->getBranch()->getCode() : 'N/A');
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('I' . $num, $position->getSection() ? $position->getSection()->getCode() : 'N/A');
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('J' . $num, 1);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('K' . $num, $claim->getClaimType()->getCode());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('L' . $num, $claim->getClaimCategory()->getCode());
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue('M' . $num, number_format($claim->getClaimAmount(), 2, '.', ','));
        }
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);
        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'excel-report.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;
    }

    public function submitDraftClaimsAction()
    {

        $em = $this->getDoctrine()->getManager();
        $claims = $em->getRepository('AppBundle:Claim')->findBy(
            [
                'status' => Claim::STATUS_DRAFT,
                'position' => $this->getUser()->getLoginWithPosition()
            ]
        );
        foreach ($claims as $claim) {
            $claim->setStatus(Claim::STATUS_PENDING);
            $em->persist($claim);
        }
        $em->flush();
        $this->addFlash('sonata_flash_success', 'Submit successfully');
        $url = $this->admin->generateUrl('list', ['type' => 'current']);

        return new RedirectResponse($url);
    }

    public function listUserSubmissionForAction()
    {
        return $this->render("AppBundle:SonataAdmin/Claim:list_user_submission_for.html.twig");
    }

    public function firstPageCreateClaimAction()
    {
        return $this->render("AppBundle:SonataAdmin/Claim:first_page_create_claim.html.twig");
    }

    public function listOptionClaimAction()
    {
        return $this->render("@App/SonataAdmin/Claim/list_option_claim.html.twig");
    }

    public function uploadImageAction()
    {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $mediaManager = $this->get('sonata.media.manager.media');
            $media = new Media();
            $media->setContext('default');
            $media->setProviderName('sonata.media.provider.image');
            $media->setBinaryContent($request->files->get('image'));
            $mediaManager->save($media);

            $claim = $this->admin->getSubject();
            $claimMedia = new ClaimMedia();
            $claimMedia->setClaim($claim);
            $claimMedia->setMedia($media);
            $em->persist($claimMedia);
            $em->flush();

            return new JsonResponse([
                'status' => true,
                'urlImage' => $this->get('app.media.retriever')->getPublicURL($media, 'default', 'default_small'),
                'urlDelete' => $this->generateUrl('admin_app_claim_deleteImage', ['id' => $claim->getId(), 'mediaId' => $media->getId()])
            ]);
        }
    }

    public function deleteImageAction()
    {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $mediaManager = $this->get('sonata.media.manager.media');
            $em = $this->getDoctrine()->getManager();
            $claim = $this->admin->getSubject();
            $media = $mediaManager->find($request->get('mediaId'));

            $claimMedia = $em->getRepository('AppBundle:ClaimMedia')->findOneBy(['claim' => $claim, 'media' => $media]);
            $em->remove($claimMedia);
            $em->flush();
            return new JsonResponse([
                'status' => true,
            ]);
        }
    }

    public function checkerApproveAction()
    {
        $object = $this->admin->getSubject();
        $object->setStatus(Claim::STATUS_CHECKER_APPROVED);
        $this->admin->update($object);

        $this->addFlash('sonata_flash_success', 'Approved successfully');

        return new RedirectResponse($this->admin->generateUrl('list', ['type' => 'checking-each-position', 'position-id' => $object->getPosition()->getId()]));
    }

    public function checkerRejectAction()
    {
        $object = $this->admin->getSubject();

        $object->setStatus(Claim::STATUS_APPROVER_REJECTED);
        $this->admin->update($object);
        $this->addFlash('sonata_flash_success', 'Rejected successfully');

        return new RedirectResponse($this->admin->generateUrl('list', ['type' => 'checking-each-position', 'position-id' => $object->getPosition()->getId()]));
    }

    public function showAction($id = null)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $this->admin->checkAccess('show', $object);

        $preResponse = $this->preShow($request, $object);
        if ($preResponse !== null) {
            return $preResponse;
        }

        $this->admin->setSubject($object);

        if ($request->isMethod('post')) {
            if ($request->get('btn_checker_approve') == 1) {

                $urlRedirect = $this->admin->generateUrl('list', ['type' => 'checking-each-position', 'position-id' => $object->getPosition()->getId()]);
                $object->setCheckerUpdatedAt(new \DateTime());
                $object->setCheckerRemark($request->get('checker-remark'));
                $object->setStatus(Claim::STATUS_CHECKER_APPROVED);
            } else if ($request->get('btn_checker_reject') == 1) {
                $urlRedirect = $this->admin->generateUrl('list', ['type' => 'checking-each-position', 'position-id' => $object->getPosition()->getId()]);
                $object->setCheckerUpdatedAt(new \DateTime());
                $object->setCheckerRemark($request->get('checker-remark'));
                $object->setStatus(Claim::STATUS_CHECKER_REJECTED);
            } else if ($request->get('btn_approver_approve') == 1) {
                $urlRedirect = $this->admin->generateUrl('list', ['type' => 'approving-each-position', 'position-id' => $object->getPosition()->getId()]);
                $object->setCheckerUpdatedAt(new \DateTime());
                $object->setStatus(Claim::STATUS_APPROVER_APPROVED);
            } else if ($request->get('btn_approver_reject') == 1) {
                $urlRedirect = $this->admin->generateUrl('list', ['type' => 'approving-each-position', 'position-id' => $object->getPosition()->getId()]);
                $object->setCheckerUpdatedAt(new \DateTime());
                $object->setStatus(Claim::STATUS_APPROVER_REJECTED);
            } else if ($request->get('btn_hr_reject') == 1) {
                $urlRedirect = $this->admin->generateUrl('list', ['type' => 'hr-each-position', 'position-id' => $object->getPosition()->getId()]);
                $object->setCheckerUpdatedAt(new \DateTime());
                $object->setStatus(Claim::STATUS_HR_REJECTED);
            } else if ($request->get('btn_hr_delete') == 1) {

                $this->admin->delete($object);
                $urlRedirect = $this->admin->generateUrl('list', ['type' => 'hr-each-position', 'position-id' => $object->getPosition()->getId()]);
                $this->addFlash(
                    'sonata_flash_success',
                    $this->trans(
                        'flash_delete_success',
                        array('%name%' => 'claim'),
                        'SonataAdminBundle'
                    )
                );
                return new RedirectResponse($urlRedirect);
            } else {
                $urlRedirect = $this->admin->generateUrl('firstPageCreateClaim');
                $object->setStatus(Claim::STATUS_PENDING);
                $object->setSubmissionRemarks($request->get('employee-remark'));
            }

            $this->admin->update($object);
            $this->addFlash(
                'sonata_flash_success',
                $this->trans(
                    'flash_edit_success',
                    array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                    'SonataAdminBundle'
                )
            );
            return new RedirectResponse($urlRedirect);
        }

        return $this->render($this->admin->getTemplate('show'), array(
            'action' => 'show',
            'object' => $object,
            'elements' => $this->admin->getShow(),
        ), null);
    }

    /**
     * Edit action.
     *
     * @param int|string|null $id
     *
     * @return Response|RedirectResponse
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function editAction($id = null)
    {
        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';

        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id : %s', $id));
        }

        $this->admin->checkAccess('edit', $object);

        $preResponse = $this->preEdit($request, $object);
        if ($preResponse !== null) {
            return $preResponse;
        }

        $this->admin->setSubject($object);

        /** @var $form Form */
        $form = $this->admin->getForm();
        $form->setData($object);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            //TODO: remove this check for 4.0
            if (method_exists($this->admin, 'preValidate')) {
                $this->admin->preValidate($object);
            }
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                try {
                    $object->setStatus(Claim::STATUS_DRAFT);
                    $object = $this->admin->update($object);

                    if ($this->isXmlHttpRequest()) {
                        return $this->renderJson(array(
                            'result' => 'ok',
                            'objectId' => $this->admin->getNormalizedIdentifier($object),
                            'objectName' => $this->escapeHtml($this->admin->toString($object)),
                        ), 200, array());
                    }

//                    $this->addFlash(
//                        'sonata_flash_success',
//                        $this->trans(
//                            'flash_edit_success',
//                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
//                            'SonataAdminBundle'
//                        )
//                    );

                    // redirect to edit mode
                    return $this->redirectTo($object);
                } catch (ModelManagerException $e) {
                    $this->handleModelManagerException($e);

                    $isFormValid = false;
                } catch (LockException $e) {
                    $this->addFlash('sonata_flash_error', $this->trans('flash_lock_error', array(
                        '%name%' => $this->escapeHtml($this->admin->toString($object)),
                        '%link_start%' => '<a href="' . $this->admin->generateObjectUrl('edit', $object) . '">',
                        '%link_end%' => '</a>',
                    ), 'SonataAdminBundle'));
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->addFlash(
                        'sonata_flash_error',
                        $this->trans(
                            'flash_edit_error',
                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                            'SonataAdminBundle'
                        )
                    );
                }
            } elseif ($this->isPreviewRequested()) {
                // enable the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'edit',
            'form' => $view,
            'object' => $object,
        ), null);
    }


    /**
     * Create action.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws AccessDeniedException If access is not granted
     */
    public function createAction()
    {
        $request = $this->getRequest();
        // the key used to lookup the template
        $templateKey = 'edit';

        $this->admin->checkAccess('create');

        $class = new \ReflectionClass($this->admin->hasActiveSubClass() ? $this->admin->getActiveSubClass() : $this->admin->getClass());

        if ($class->isAbstract()) {
            return $this->render(
                'SonataAdminBundle:CRUD:select_subclass.html.twig',
                array(
                    'base_template' => $this->getBaseTemplate(),
                    'admin' => $this->admin,
                    'action' => 'create',
                ),
                null,
                $request
            );
        }

        $object = $this->admin->getNewInstance();

        $preResponse = $this->preCreate($request, $object);
        if ($preResponse !== null) {
            return $preResponse;
        }

        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            //TODO: remove this check for 4.0
            if (method_exists($this->admin, 'preValidate')) {
                $this->admin->preValidate($object);
            }
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid && (!$this->isInPreviewMode($request) || $this->isPreviewApproved($request))) {
                $this->admin->checkAccess('create', $object);

                try {
                    $object = $this->admin->create($object);

                    if ($this->isXmlHttpRequest()) {
                        return $this->renderJson(array(
                            'result' => 'ok',
                            'objectId' => $this->admin->getNormalizedIdentifier($object),
                        ), 200, array());
                    }

//                    $this->addFlash(
//                        'sonata_flash_success',
//                        $this->trans(
//                            'flash_create_success',
//                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
//                            'SonataAdminBundle'
//                        )
//                    );

                    // redirect to edit mode
                    return $this->redirectTo($object);
                } catch (ModelManagerException $e) {
                    $this->handleModelManagerException($e);

                    $isFormValid = false;
                }
            }

            // show an error message if the form failed validation
            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->addFlash(
                        'sonata_flash_error',
                        $this->trans(
                            'flash_create_error',
                            array('%name%' => $this->escapeHtml($this->admin->toString($object))),
                            'SonataAdminBundle'
                        )
                    );
                }
            } elseif ($this->isPreviewRequested()) {
                // pick the preview template if the form was valid and preview was requested
                $templateKey = 'preview';
                $this->admin->getShow();
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'create',
            'form' => $view,
            'object' => $object,
        ), null);
    }

    /**
     * Redirect the user depend on this choice.
     *
     * @param object $object
     *
     * @return RedirectResponse
     */
    protected function redirectTo($object)
    {
        $request = $this->getRequest();

        $url = false;

        if (null !== $request->get('btn_create_and_edit_onbehalf')) {
            $url = $this->admin->generateObjectUrl('edit', $object, ['type' => 'onbehalf', 'position-id' => $request->get('position-id')]);
        }
        if (null !== $request->get('btn_create_and_edit')) {
            $url = $this->admin->generateObjectUrl('edit', $object);
        }
        if (null !== $request->get('btn_edit_and_show_onbehalf')) {
            $url = $this->admin->generateObjectUrl('show', $object, ['type' => 'employee-preview-claim', 'position-id' => $request->get('position-id')]);
        }
        if (null !== $request->get('btn_edit_and_show')) {
            $url = $this->admin->generateObjectUrl('show', $object, ['type' => 'employee-preview-claim']);
        }
        if (null !== $request->get('btn_update_and_list')) {
            $url = $this->admin->generateUrl('list');
        }
        if (null !== $request->get('btn_create_and_list')) {
            $url = $this->admin->generateUrl('list');
        }

        if (null !== $request->get('btn_create_and_create')) {
            $params = array();
            if ($this->admin->hasActiveSubClass()) {
                $params['subclass'] = $request->get('subclass');
            }
            $url = $this->admin->generateUrl('create', $params);
        }

        if ($this->getRestMethod() === 'DELETE') {
            $url = $this->admin->generateUrl('list');
        }

        if (!$url) {
            foreach (array('edit', 'show') as $route) {
                if ($this->admin->hasRoute($route) && $this->admin->isGranted(strtoupper($route), $object)) {
                    $url = $this->admin->generateObjectUrl($route, $object);
                    break;
                }
            }
        }

        if (!$url) {
            $url = $this->admin->generateUrl('list');
        }

        return new RedirectResponse($url);
    }


}