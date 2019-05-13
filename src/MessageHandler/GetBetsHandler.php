<?php

namespace App\MessageHandler;

use App\Entity\Bet;
use App\Message\GetBets;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;

class GetBetsHandler implements MessageHandlerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(GetBets $message)
    {
        $repository =
            $this->entityManager->getRepository(Bet::class);

        if ($message->game !== null) {
            return $repository->findBy(['game' => $message->game]);
        }

        return $repository->findAll();
    }
}
