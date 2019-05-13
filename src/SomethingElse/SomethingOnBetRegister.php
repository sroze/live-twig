<?php

namespace App\SomethingElse;

use App\Message\RegisterBet;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class SomethingOnBetRegister implements MessageSubscriberInterface
{
    public function __invoke(RegisterBet $message)
    {
        var_dump('for 3rd party!', $message);
    }

    /**
     * {@inheritdoc}
     */
    public static function getHandledMessages(): iterable
    {
        yield RegisterBet::class => [
            'transport' => 'events_3rdparty',
        ];
    }
}
