<?php

namespace Symfony\Component\LiveTwig\Message;

class LiveTwigUpdate
{
    private $tags;

    public function __construct(array $tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }
}
