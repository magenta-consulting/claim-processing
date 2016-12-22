<?php
namespace Application\Sonata\MediaBundle\Provider;

use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\ImageProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;



use Gaufrette\Filesystem;
use Imagine\Image\ImagineInterface;
use Sonata\CoreBundle\Model\Metadata;
use Sonata\MediaBundle\CDN\CDNInterface;
use Sonata\MediaBundle\Generator\GeneratorInterface;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;

use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class AppImageProvider extends ImageProvider
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct($name, Filesystem $filesystem, CDNInterface $cdn, GeneratorInterface $pathGenerator, ThumbnailInterface $thumbnail, array $allowedExtensions, array $allowedMimeTypes, ImagineInterface $adapter, MetadataBuilderInterface $metadata = null,ContainerInterface $container)
    {
        parent::__construct($name, $filesystem, $cdn, $pathGenerator, $thumbnail, $allowedExtensions, $allowedMimeTypes, $adapter, $metadata);
        $this->container = $container;

    }


    /**
     * {@inheritdoc}
     */
    public function generatePublicUrl(MediaInterface $media, $format)
    {
        if ($format == 'reference') {
            $path = $this->getReferenceImage($media);
        } else {
            $path = $this->thumbnail->generatePublicUrl($this, $media, $format);
        }

      if(!empty($this->container->getParameter('s3_directory'))){
          $path = $this->container->getParameter('s3_directory') . '/' . $path;
      }
        return $this->getCdn()->getPath($path, $media->getCdnIsFlushable());
    }

}