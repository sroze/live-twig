<?php

namespace App\Message;

class GetBets
{
    public $game = null;

    public static function forGame(string $game)
    {
        $query = new self();
        $query->game = $game;

        return $query;
    }
}
