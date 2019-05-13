<?php

namespace Symfony\Component\LiveTwig\Event;

use Symfony\Contracts\EventDispatcher\Event;

class RenderedLiveFragment extends Event
{
    public $identifier;
    public $url;
    public $tags;

    /**
     * @param string[] $tags
     */
    public function __construct(string $identifier, string $url, array $tags)
    {
        $this->identifier = $identifier;
        $this->url = $url;
        $this->tags = $tags;
    }
}
