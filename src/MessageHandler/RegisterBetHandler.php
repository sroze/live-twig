<?php

namespace App\MessageHandler;

use App\Entity\Bet;
use App\Message\RegisterBet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RegisterBetHandler implements MessageHandlerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(RegisterBet $message)
    {
        $bet = new Bet();
        $bet->game = $message->getGame();
        $bet->rightScore = $message->getRightScore();
        $bet->leftScore = $message->getLeftScore();

        $this->entityManager->persist($bet);
        $this->entityManager->flush();
    }
}
