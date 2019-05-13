<?php

namespace Symfony\Component\LiveTwig\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Live\Subscription;
use Symfony\Component\Live\SubscriptionList;
use Symfony\Component\LiveTwig\Event\RenderedLiveFragment;
use Twig\Environment;

class InjectSubscriberJavaScriptListener implements EventSubscriberInterface
{
    private $twig;

    /**
     * @var RenderedLiveFragment[]
     */
    private $subscriptions = [];
    private $hubUrl;

    public function __construct(Environment $twig, string $hubUrl)
    {
        $this->twig = $twig;
        $this->hubUrl = $hubUrl;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', -128),
            RenderedLiveFragment::class => 'onFragmentRenderer',
        );
    }

    public function onFragmentRenderer(RenderedLiveFragment $event)
    {
        $this->subscriptions[] = $event;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();
        $content = $response->getContent();
        $pos = strripos($content, '</body>');

        if (false === $pos) {
            return;
        }

        $toolbar = "\n".str_replace("\n", '', $this->twig->render(
            '@LiveTwig/subscriber_js.html.twig',
            array(
                'hub_url' => $this->hubUrl,
                'subscriptions' => $this->subscriptions,
            )
        ))."\n";

        $content = substr($content, 0, $pos).$toolbar.substr($content, $pos);
        $response->setContent($content);
    }
}
