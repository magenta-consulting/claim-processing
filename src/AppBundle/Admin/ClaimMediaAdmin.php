<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use AppBundle\Admin\BaseAdmin;

class ClaimMediaAdmin extends BaseAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $claimMedia = $this->getSubject();
        $fileFieldOptions = array('label' => 'Images','provider' => 'sonata.media.provider.image','context'  => 'default');
        if ($this->hasSubject() && $claimMedia->getMedia()) {
            $container = $this->getConfigurationPool()->getContainer();
            $fullPath = $container->get('app.media.retriever')->getPublicURL($claimMedia->getMedia());
            $fileFieldOptions['help'] = '<img src="'.$fullPath.'" class="admin-preview" />';
        }
        $formMapper->add('media', 'sonata_media_type',$fileFieldOptions,$fileFieldOptions);

        $formMapper->get('media')->remove('unlink');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('media');
    }
}
