<?php

namespace Symfony\Component\LiveTwig\Event;

use Symfony\Contracts\EventDispatcher\Event;

class RenderedLiveFragment extends Event
{
    public $identifier;
    public $url;
    public $topics;

    /**
     * @param string[] $topics
     */
    public function __construct(string $identifier, string $url, array $topics)
    {
        $this->identifier = $identifier;
        $this->url = $url;
        $this->topics = $topics;
    }
}
