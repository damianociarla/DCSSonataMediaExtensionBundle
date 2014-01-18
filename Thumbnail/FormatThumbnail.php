<?php

namespace DCS\SonataMediaExtensionBundle\Thumbnail;

use Sonata\MediaBundle\Thumbnail\FormatThumbnail as BaseFormatThumbnail;
use Sonata\MediaBundle\Provider\MediaProviderInterface;
use Sonata\MediaBundle\Model\MediaInterface;

class FormatThumbnail extends BaseFormatThumbnail
{
    /**
     * @var boolean
     */
    protected $onDemand;

    /**
     * @param string $defaultFormat
     * @param boolean $onDemand
     */
    public function __construct($defaultFormat, $onDemand = false)
    {
        parent::__construct($defaultFormat);
        $this->onDemand = (bool)$onDemand;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePublicUrl(MediaProviderInterface $provider, MediaInterface $media, $format)
    {
        if ($this->onDemand) {
            $this->generateOnDemandThumbnail($provider, $media, $format);
        }

        return parent::generatePublicUrl($provider, $media, $format);
    }

    /**
     * {@inheritdoc}
     */
    public function generate(MediaProviderInterface $provider, MediaInterface $media)
    {
        if ($this->onDemand) {
            return;
        }

        parent::generate($provider, $media);
    }

    public function generateOnDemandThumbnail(MediaProviderInterface $provider, MediaInterface $media, $format)
    {
        $referenceFile = $provider->getReferenceFile($media);

        if (!$referenceFile->exists()) {
            return;
        }

        $formats = $provider->getFormats();

        if (!array_key_exists($format, $formats)) {
            throw new \Exception(sprintf('Format %s does not exists', $format));
        }

        $settings  = $formats[$format];
        $thumbPath = $provider->generatePrivateUrl($media, $format);

        if ($thumbPath && $provider->getFilesystem()->has($thumbPath)) {
            return;
        }

        if (substr($format, 0, strlen($media->getContext())) == $media->getContext() || $format === 'admin') {
            $provider->getResizer()->resize(
                $media,
                $referenceFile,
                $provider->getFilesystem()->get($thumbPath, true),
                $this->getExtension($media),
                $settings
            );
        }
    }
}
