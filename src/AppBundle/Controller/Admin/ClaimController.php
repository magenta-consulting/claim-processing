<?php
// src/AppBundle/Controller/CRUDController.php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Claim;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ClaimController extends Controller
{
    public function checkerApproveAction()
    {
        $object = $this->admin->getSubject();
        $object->setStatus(Claim::STATUS_CHECKER_APPROVED);
        $this->admin->update($object);

        $this->addFlash('sonata_flash_success', 'Approved successfully');

        return new RedirectResponse($this->admin->generateUrl('list',['type'=>'checking-each-position','position-id'=>$object->getPosition()->getId()]));
    }
    public function checkerRejectAction()
    {
        $object = $this->admin->getSubject();

        $object->setStatus(Claim::STATUS_APPROVER_REJECTED);
        $this->admin->update($object);
        $this->addFlash('sonata_flash_success', 'Rejected successfully');

        return new RedirectResponse($this->admin->generateUrl('list',['type'=>'checking-each-position','position-id'=>$object->getPosition()->getId()]));
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

        if($request->isMethod('post')){
            $status = $request->get('btn_approve') == 1 ? Claim::STATUS_CHECKER_APPROVED: Claim::STATUS_APPROVER_REJECTED;
            $object->setStatus($status);
            $object->setCheckerRemark($request->get('remark'));
            $this->admin->update($object);

            return new RedirectResponse($this->admin->generateUrl('list',['type'=>'checking-each-position','position-id'=>$object->getPosition()->getId()]));
        }

        return $this->render($this->admin->getTemplate('show'), array(
            'action' => 'show',
            'object' => $object,
            'elements' => $this->admin->getShow(),
        ), null);
    }
}