<?php

namespace Symfony\Component\Live\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Live\Subscription;
use Symfony\Component\Live\SubscriptionList;
use Twig\Environment;

class InjectSubscriberJavaScriptListener implements EventSubscriberInterface
{
    private $twig;

    // TODO: Reset subscriptions with `kernel.reset`
    private $extraSubscriptions = [];

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
        $subscriptions = $this->getSubscriptions($response);
        if (empty($subscriptions)) {
            return;
        }

        $content = $response->getContent();
        $pos = strripos($content, '</body>');

        if (false !== $pos) {
            $toolbar = "\n".str_replace("\n", '', $this->twig->render(
                '@Live/subscriber_js.html.twig',
                array(
                    'subscriptions' => (new SubscriptionList($subscriptions))->asArray(),
                )
            ))."\n";

            $content = substr($content, 0, $pos).$toolbar.substr($content, $pos);
            $response->setContent($content);
        }
    }

    public function registerExtraSubscription(Subscription $subscription)
    {
        $this->extraSubscriptions[] = $subscription;
    }

    private function getSubscriptions(Response $response)
    {
        $subscriptions = [];
        if (null !== $header = $response->headers->get('X-Symfony-Live-Subscriptions')) {
            $subscriptions = SubscriptionList::fromString($header)->getSubscriptions();
        }

        return array_merge($subscriptions, $this->extraSubscriptions);
    }
}
