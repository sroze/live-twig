<?php

namespace App\Controller;

use App\Message\GetBets;
use App\Message\RegisterBet;
use App\Message\ReportGameResult;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $bets = $this->bus->dispatch(new GetBets());

        return $this->render('index.html.twig', [
            'bets' => $bets,
        ]);
    }

    /**
     * @Route("/bet", methods={"POST"}, name="do_bet")
     */
    public function bet(Request $request)
    {
        $this->bus->dispatch(new RegisterBet(
            $request->request->get('game'),
            $request->request->getInt('left'),
            $request->request->getInt('right')
        ));

        return $this->redirectToRoute('home');
    }
}
