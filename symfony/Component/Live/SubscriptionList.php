<?php

namespace Symfony\Component\Live;

class SubscriptionList
{
    /**
     * @var Subscription[]
     */
    private $subscriptions;

    public function __construct(array $subscriptions)
    {
        $this->subscriptions = $subscriptions;
    }

    public static function fromString(string $string)
    {
        $list = json_decode(base64_decode($string), true);

        return new self(array_map(function(string $subscription) {
            return Subscription::fromString($subscription);
        }, $list));
    }

    public function toString()
    {
        return base64_encode(json_encode(array_map(function(Subscription $subscription) {
            return $subscription->toString();
        }, $this->subscriptions)));
    }

    public function asArray()
    {
        return array_map(function(Subscription $subscription) {
            $array = $subscription->asArray();
            $array['token'] = $subscription->toString();

            return $array;
        }, $this->subscriptions);
    }
}
