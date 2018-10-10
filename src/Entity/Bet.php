<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Bet
{
    /**
     * @ORM\Id
     * @ORM\Column
     * @ORM\GeneratedValue(strategy="UUID")
     */
    public $uuid;

    /**
     * @ORM\Column(type="string")
     */
    public $game;

    /**
     * @ORM\Column(type="integer")
     */
    public $leftScore;

    /**
     * @ORM\Column(type="integer")
     */
    public $rightScore;
}
