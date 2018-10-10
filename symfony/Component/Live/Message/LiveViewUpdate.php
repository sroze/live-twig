<?php

namespace Symfony\Component\Live\Message;

class LiveViewUpdate
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
