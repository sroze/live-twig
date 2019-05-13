<?php

namespace App\Controller;

use App\Message\GetBets;
use App\Message\RegisterBet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\LiveTwig\Message\LiveTwigUpdate;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    use HandleTrait;

    private $messageBus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->messageBus = $bus;
    }

    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/bets/{game}", name="bets_for_game")
     */
    public function betsForGame($game)
    {
        return $this->render('game.html.twig', [
            'game' => $game,
        ]);
    }

    /**
     * @Route("/bet", methods={"POST"}, name="do_bet")
     */
    public function bet(Request $request)
    {
        $this->messageBus->dispatch(new RegisterBet(
            $game = $request->request->get('game'),
            $request->request->getInt('left'),
            $request->request->getInt('right')
        ));

        $this->messageBus->dispatch(new LiveTwigUpdate([
            'bets',
            'game-'.$game,
        ]));

        return $this->redirectToRoute('home');
    }

    public function betList(string $game = null)
    {
        $bets = $this->handle(
            $game !== null ? GetBets::forGame($game) : GetBets::all()
        );

        return $this->render('bets/list.html.twig', [
            'bets' => $bets,
        ]);
    }
}
