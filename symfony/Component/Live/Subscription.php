<?php

namespace Symfony\Component\Live;

use Symfony\Component\HttpKernel\Controller\ControllerReference;

class Subscription
{
    private $tags;
    private $contentLocation;
    private $source;

    public function __construct(array $tags, string $contentLocation, ControllerReference $source)
    {
        $this->tags = $tags;
        $this->contentLocation = $contentLocation;
        $this->source = $source;
    }

    public static function fromString(string $string)
    {
        $decoded = json_decode(base64_decode($string), true);

        return new self($decoded['tags'], $decoded['contentLocation'], unserialize(base64_decode($decoded['source'])));
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
            'source' => base64_encode(serialize($this->source)),
        ];
    }

    public function getSource()
    {
        return $this->source;
    }
}
