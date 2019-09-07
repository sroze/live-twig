<?php

namespace Symfony\Component\LiveTwig\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\LiveTwig\Event\RenderedLiveFragment;
use Twig\Environment;

class InjectSubscriberJavaScriptListener implements EventSubscriberInterface
{
    private $twig;
    private $hasSubscriptions = false;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', -128),
            RenderedLiveFragment::class => 'onFragmentRenderer',
        );
    }

    public function onFragmentRenderer()
    {
        $this->hasSubscriptions = true;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest() || !$this->hasSubscriptions) {
            return;
        }

        $response = $event->getResponse();
        $content = $response->getContent();
        $pos = strripos($content, '</body>');

        if (false === $pos) {
            return;
        }

        $toolbar = "\n".str_replace("\n", '', $this->twig->render('@LiveTwig/subscriber_js.html.twig'))."\n";

        $content = substr($content, 0, $pos).$toolbar.substr($content, $pos);
        $response->setContent($content);
    }
}
