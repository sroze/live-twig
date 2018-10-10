<?php

namespace App\Message;

class RegisterBet
{
    private $game;
    private $leftScore;
    private $rightScore;

    public function __construct(string $game, int $leftScore, int $rightScore)
    {
        $this->game = $game;
        $this->leftScore = $leftScore;
        $this->rightScore = $rightScore;
    }

    public function getGame(): string
    {
        return $this->game;
    }

    public function getLeftScore(): int
    {
        return $this->leftScore;
    }

    public function getRightScore(): int
    {
        return $this->rightScore;
    }
}
