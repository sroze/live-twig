<?php

namespace Symfony\Component\Live\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\Live\EventListener\InjectSubscriberJavaScriptListener;
use Symfony\Component\Live\Subscription;

class LiveExtension extends \Twig_Extension
{
    private $subscriberListener;
    private $requestStack;

    public function __construct(InjectSubscriberJavaScriptListener $subscriberListener, RequestStack $requestStack)
    {
        $this->subscriberListener = $subscriberListener;
        $this->requestStack = $requestStack;
    }

    public function getTokenParsers()
    {
        return [
            new LiveSubscriberTokenParser(),
        ];
    }

    public function registerSubscription(string $tag, string $location)
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            throw new \RuntimeException('Needs to be in a request context');
        }

        $source = new ControllerReference($request->attributes->get('_controller'), $request->attributes->all(), $request->query->all());

        $this->subscriberListener->registerExtraSubscription(
            new Subscription([$tag], $location, $source)
        );
    }
}
