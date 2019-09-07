<?php

namespace Symfony\Component\LiveTwig\MessageHandler;

use Symfony\Component\LiveTwig\Message\LiveTwigUpdate;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;

class LiveTwigUpdateHandler
{
    private $hubUrl;
    private $hubToken;

    public function __construct(string $hubUrl, string $hubToken)
    {
        $this->hubUrl = $hubUrl;
        $this->hubToken = $hubToken;
    }

    public function __invoke(LiveTwigUpdate $message)
    {
        $publisher = new Publisher(
            $this->hubUrl,
            function() {
                return $this->hubToken;
            }
        );

        $publisher(new Update(
            $message->getTags(),
            json_encode([])
        ));
    }
}
