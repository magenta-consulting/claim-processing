<?php
// src/Solutions/AppBundle/Services/Core/Media/MediaRetriever.php

namespace AppBundle\Services\Core;

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
//        $provider = $this->container->get('sonata.media.provider.image');
//        $urlNotTrue =  $provider->generatePublicUrl($media,$format);
//        $dir = $this->container->getParameter('s3_directory');
//        $region = $this->container->getParameter('s3_region');
//        $host = 'https://s3-'.$region.'.amazonaws.com';
//        $bucket = $this->container->getParameter('s3_bucket_name');
//        $arr= explode($bucket,$urlNotTrue);
//        $endUrl =  $arr[1];
//        return $host.'/'.$bucket.'/'.$dir .($media->getContext()===null?'/':''). $endUrl;

        //local
        $provider = $this->container->get('sonata.media.provider.image');
        return $provider->generatePublicUrl($media, $format);
    }


}
