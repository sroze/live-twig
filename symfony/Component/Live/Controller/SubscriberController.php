<?php

namespace Symfony\Component\Live\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;
use Symfony\Component\Live\Message\LiveViewUpdate;
use Symfony\Component\Live\Subscription;
use Symfony\Component\Live\Transport\LiveTransportInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Receiver\StopWhenTimeLimitIsReachedReceiver;
use Symfony\Component\Messenger\Transport\TransportInterface;

class SubscriberController
{
    private $transport;
    private $fragmentRenderer;

    public function __construct(TransportInterface $transport, FragmentRendererInterface $fragmentRenderer)
    {
        $this->transport = $transport;
        $this->fragmentRenderer = $fragmentRenderer;
    }

    public function __invoke(Request $request, $subscriptionToken)
    {
        if (!$this->transport instanceof LiveTransportInterface) {
            throw new \RuntimeException('The transport "live" needs to be a live transport. Try to prefix its DSN with "live+" and use the "{tags}" placeholder.');
        }

        $subscription = Subscription::fromString($subscriptionToken);
        $transport = $this->transport->transportFor($subscription);

        return new StreamedResponse(function() use ($transport, $subscription, $request) {
            $receiver = (new StopWhenTimeLimitIsReachedReceiver($transport, 30));
            $receiver->receive(function (?Envelope $envelope) use ($subscription, $request) {
                if (null === $envelope) {
                    return;
                }

                $message = $envelope->getMessage();
                if (!$message instanceof LiveViewUpdate) {
                    throw new \RuntimeException(sprintf('Received a message of type "%s" while expecting only "%s".', get_class($message), LiveViewUpdate::class));
                }

                $renderFragment = $this->fragmentRenderer->render($subscription->getSource(), $request);
                $extractedSubscription = $this->extract($renderFragment->getContent(), $subscription->getContentLocation());

                echo "data: ".json_encode(['html' => $extractedSubscription])."\n\n";

                ob_flush();
                flush();
            });
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
        ]);
    }

    private function extract(string $content, string $location)
    {
        if (1 !== preg_match('#\<div id="live-'.$location.'"\>(.*)\<\/div>#s', $content, $matches)) {
            throw new \RuntimeException('Could not extract subscription\'s content');
        }

        return $matches[1];
    }
}
