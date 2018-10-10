<?php

namespace Symfony\Component\Live;

class Subscription
{
    private $tags;
    private $contentLocation;

    public function __construct(array $tags, string $contentLocation)
    {
        $this->tags = $tags;
        $this->contentLocation = $contentLocation;
    }

    public static function fromString(string $string)
    {
        $decoded = json_decode(base64_decode($string), true);

        return new self($decoded['tags'], $decoded['contentLocation']);
    }

    public function toString()
    {
        return base64_encode(json_encode($this->asArray()));
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    public function getContentLocation(): string
    {
        return $this->contentLocation;
    }

    public function asArray(): array
    {
        return [
            'tags' => $this->tags,
            'contentLocation' => $this->contentLocation,
        ];
    }
}
