<?php

namespace Symfony\Component\Live\Transport;

use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class LiveTransportFactory implements TransportFactoryInterface
{
    private $transportFactory;

    public function __construct(TransportFactoryInterface $transportFactory)
    {
        $this->transportFactory = $transportFactory;
    }

    public function createTransport(string $dsn, array $options): TransportInterface
    {
        return new LiveTransport($this->transportFactory, $dsn, $options);
    }

    public function supports(string $dsn, array $options): bool
    {
        return 1 === preg_match('#^live\+([a-z]+):\/\/#', $dsn);
    }
}
