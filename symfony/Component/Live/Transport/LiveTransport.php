<?php

namespace Symfony\Component\Live\Transport;

use Symfony\Component\Live\Message\LiveViewUpdate;
use Symfony\Component\Live\Subscription;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class LiveTransport implements TransportInterface, LiveTransportInterface
{
    private $transportFactory;
    private $dsn;
    private $options;

    private $transportsPerDsn = [];

    public function __construct(TransportFactoryInterface $transportFactory, string $dsn, array $options)
    {
        $this->transportFactory = $transportFactory;
        $this->dsn = $dsn;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function receive(callable $handler): void
    {
        throw new \RuntimeException('The live transport can\'t be used directly, you need to use the `transportFor` method');
    }

    /**
     * {@inheritdoc}
     */
    public function stop(): void
    {
        throw new \RuntimeException('The live transport can\'t be used directly, you need to use the `transportFor` method');
    }

    /**
     * {@inheritdoc}
     */
    public function send(Envelope $envelope): Envelope
    {
        $message = $envelope->getMessage();
        if (!$message instanceof LiveViewUpdate) {
            throw new \RuntimeException('The live transport can\'t be used directly, you need to use the `transportFor` method');
        }

        return $this->getTransport($message->getTags())->send($envelope);
    }

    public function transportFor(Subscription $subscription): TransportInterface
    {
        return $this->getTransport($subscription->getTags());
    }

    private function getTransport(array $tags): TransportInterface
    {
        $dsn = str_replace('{tags}', implode(',', $tags), substr($this->dsn, 5));

        if (!array_key_exists($dsn, $this->transportsPerDsn)) {
            $this->transportsPerDsn[$dsn] = $this->transportFactory->createTransport($dsn, $this->options);
        }

        return $this->transportsPerDsn[$dsn];
    }
}
