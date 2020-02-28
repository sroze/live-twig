<?php

namespace Symfony\Component\LiveTwig\MessageHandler;

use Symfony\Component\LiveTwig\Message\LiveTwigUpdate;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;

class LiveTwigUpdateHandler
{
    private $publisher;

    public function __construct(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    public function __invoke(LiveTwigUpdate $message)
    {
        $publisher = $this->publisher;
        $publisher(new Update(
            $message->getTags(),
            json_encode([])
        ));
    }
}
