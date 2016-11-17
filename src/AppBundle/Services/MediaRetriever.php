<?php
// src/Solutions/AppBundle/Services/Core/Media/MediaRetriever.php

namespace AppBundle\Services;

use Sonata\MediaBundle\Model\Media;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class MediaRetriever
{
    use ContainerAwareTrait;

    /**
     * @param int $id
     * @return Media
     */
    public function findOneById($id)
    {
        $mediaManager = $this->get('sonata.media.manager.media');
        return $mediaManager->findOneBy(array('id' => $id));
    }

    public function getPublicURL(Media $media, $context = 'default', $format = 'reference')
    {
        //local
        $provider = $this->container->get('sonata.media.provider.image');
        return $provider->generatePublicUrl($media, $format);
    }


}
