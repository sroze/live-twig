<?php

namespace App\Message;

class GetBets
{
    public $game = null;

    private function __construct()
    {
    }

    public static function all()
    {
        return new self();
    }

    public static function forGame(string $game)
    {
        $query = new self();
        $query->game = $game;

        return $query;
    }
}
