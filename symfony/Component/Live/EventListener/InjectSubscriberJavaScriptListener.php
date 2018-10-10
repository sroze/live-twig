<?php

namespace Symfony\Component\Live\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Live\SubscriptionList;
use Twig\Environment;

class InjectSubscriberJavaScriptListener implements EventSubscriberInterface
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', -128),
        );
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        if (null === $subscriptions = $response->headers->get('X-Symfony-Live-Subscriptions')) {
            return;
        }

        $content = $response->getContent();
        $pos = strripos($content, '</body>');

        if (false !== $pos) {
            $toolbar = "\n".str_replace("\n", '', $this->twig->render(
                '@Live/subscriber_js.html.twig',
                array(
                    'subscriptions' => SubscriptionList::fromString($subscriptions)->asArray(),
                )
            ))."\n";

            $content = substr($content, 0, $pos).$toolbar.substr($content, $pos);
            $response->setContent($content);
        }
    }
}
