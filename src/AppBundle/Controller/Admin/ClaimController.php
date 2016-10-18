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
}