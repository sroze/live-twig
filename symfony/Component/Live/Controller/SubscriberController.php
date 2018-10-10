<?php

namespace Symfony\Component\Live\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Live\Message\LiveViewUpdate;
use Symfony\Component\Live\Subscription;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Enhancers\StopWhenTimeLimitIsReachedReceiver;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class SubscriberController
{
    private $transport;

    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    public function __invoke($subscriptionToken)
    {
        $token = Subscription::fromString($subscriptionToken);

        return new StreamedResponse(function() use ($token) {
            $receiver = (new StopWhenTimeLimitIsReachedReceiver($this->transport, 30));
            $receiver->receive(function (?Envelope $envelope) {
                if (null === $envelope) {
                    return;
                }

                $message = $envelope->getMessage();
                if (!$message instanceof LiveViewUpdate) {
                    throw new \RuntimeException(sprintf('Received a message of type "%s" while expecting only "%s".', get_class($message), LiveViewUpdate::class));
                }

                echo "plop" . "\n";
                echo implode(',', $message->getTags()) . "\n";
                flush();
            });
        });
    }
}
