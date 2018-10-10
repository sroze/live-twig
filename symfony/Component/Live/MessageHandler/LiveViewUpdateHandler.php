<?php

namespace Symfony\Component\Live\MessageHandler;

use Symfony\Component\Live\Message\LiveViewUpdate;

class LiveViewUpdateHandler
{
    public function __invoke(LiveViewUpdate $message)
    {
        var_dump($message);
        exit;
    }
}
