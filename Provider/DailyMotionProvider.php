<?php

namespace DCS\SonataMediaExtensionBundle\Provider;

use Sonata\MediaBundle\Provider\DailyMotionProvider as BaseDailyMotionProvider;
use Sonata\MediaBundle\Model\MediaInterface;

class DailyMotionProvider extends BaseDailyMotionProvider
{
    /**
     * @var boolean
     */
    protected $onDemand;

    public function setOnDemandSettings($onDemand = false)
    {
        $this->onDemand = (bool)$onDemand;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePublicUrl(MediaInterface $media, $format)
    {
        if ($this->onDemand) {
            $this->thumbnail->generateOnDemandThumbnail($this, $media, $format);
        }

        return parent::generatePublicUrl($media, $format);
    }
}