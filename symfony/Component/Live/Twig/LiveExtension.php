<?php

namespace Symfony\Component\Live\Twig;

use Symfony\Component\Live\EventListener\InjectSubscriberJavaScriptListener;
use Symfony\Component\Live\Subscription;

class LiveExtension extends \Twig_Extension
{
    private $subscriberListener;

    public function __construct(InjectSubscriberJavaScriptListener $subscriberListener)
    {
        $this->subscriberListener = $subscriberListener;
    }

    public function getTokenParsers()
    {
        return [
            new LiveSubscriberTokenParser(),
        ];
    }

    public function registerSubscription(string $tag, string $location)
    {
        $this->subscriberListener->registerExtraSubscription(
            new Subscription([$tag], $location)
        );
    }
}
