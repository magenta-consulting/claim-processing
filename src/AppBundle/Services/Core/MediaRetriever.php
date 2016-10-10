<?php
// src/Solutions/AppBundle/Services/Core/Media/MediaRetriever.php

namespace AppBundle\Services\Core;

use Sonata\MediaBundle\Model\Media;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class MediaRetriever extends ControllerService
{

    /**
     * @param int $id
     * @return Media
     */
    public function findOneById($id)
    {
        $mediaManager = $this->get('sonata.media.manager.media');
        return $mediaManager->findOneBy(array('id' => $id));
    }

    public function getPublicURL(Media $media,$context='default',$format='medium')
    {
        $provider = $this->get('sonata.media.provider.image');
        $urlNotTrue =  $provider->generatePublicUrl($media,$context.'_'.$format);
        $dir = $this->getParameter('s3_directory');
        $region = $this->getParameter('s3_region');
        $host = 'https://s3-'.$region.'.amazonaws.com';
        $bucket = $this->getParameter('s3_bucket_name');


        $arr= explode($bucket,$urlNotTrue);
        $endUrl =  $arr[1];
        return $host.'/'.$bucket.'/'.$dir . $endUrl;

    }


}
