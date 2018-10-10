<?php

namespace Symfony\Component\Live\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;
use Symfony\Component\Live\Message\LiveViewUpdate;
use Symfony\Component\Live\Subscription;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Enhancers\StopWhenTimeLimitIsReachedReceiver;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
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
        $token = Subscription::fromString($subscriptionToken);

        return new StreamedResponse(function() use ($token, $request) {
            $receiver = (new StopWhenTimeLimitIsReachedReceiver($this->transport, 30));
            $receiver->receive(function (?Envelope $envelope) use ($token, $request) {
                if (null === $envelope) {
                    return;
                }

                $message = $envelope->getMessage();
                if (!$message instanceof LiveViewUpdate) {
                    throw new \RuntimeException(sprintf('Received a message of type "%s" while expecting only "%s".', get_class($message), LiveViewUpdate::class));
                }


                $renderFragment = $this->fragmentRenderer->render($token->getSource(), $request);
                $extractedSubscription = $this->extract($renderFragment->getContent(), $token->getContentLocation());

                echo "UPDATE" . "\n";
                echo $extractedSubscription. "\n";
                flush();
            });
        });
    }

    private function extract(string $content, string $location)
    {
        if (1 !== preg_match('#\<div id="live-'.$location.'"\>(.*)\<\/div>#s', $content, $matches)) {
            throw new \RuntimeException('Could not extract subscription\'s content');
        }

        return $matches[1];
    }
}
