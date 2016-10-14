<?php
namespace Application\Sonata\MediaBundle\Provider;


use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\Provider\FileProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;


use Gaufrette\Filesystem;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Model\Metadata;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\MediaBundle\CDN\CDNInterface;
use Sonata\MediaBundle\Extra\ApiMediaFile;
use Sonata\MediaBundle\Generator\GeneratorInterface;
use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;

use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;


class AppFileProvider extends FileProvider
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct($name, Filesystem $filesystem, CDNInterface $cdn, GeneratorInterface $pathGenerator, ThumbnailInterface $thumbnail, array $allowedExtensions = array(), array $allowedMimeTypes = array(), MetadataBuilderInterface $metadata = null,ContainerInterface $container)
    {
        parent::__construct($name, $filesystem, $cdn, $pathGenerator, $thumbnail,$allowedExtensions,$allowedMimeTypes,$metadata);
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
            // @todo: fix the asset path
            $path = sprintf('sonatamedia/files/%s/file.png', $format);
        }
        if(!empty($this->container->getParameter('s3_directory'))){
            $path = $this->container->getParameter('s3_directory') . '/' . $path;
        }
        return $this->getCdn()->getPath($path, $media->getCdnIsFlushable());
    }
}