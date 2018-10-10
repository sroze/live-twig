<?php

namespace Symfony\Component\Live\Transport;

use Symfony\Component\Live\Subscription;
use Symfony\Component\Messenger\Transport\TransportInterface;

interface LiveTransportInterface
{
    public function transportFor(Subscription $subscription) : TransportInterface;
}
