<?php

namespace App\Controller;
use App\Entity\Empresa;
use App\Entity\Players;
use App\Entity\PlayersOnline;
use App\Entity\PlayersOnlineHoras;
use App\Repository\CfopNaoMovimentaRepository;
use App\Repository\PlayersOnlineHorasRepository;
use App\Repository\PlayersOnlineRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PlayersRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer; 
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\DomCrawler\Crawler;
use App\Service\CrawlerService;

/**
* @Route("/api",name="api")
*/
class IndexController extends AbstractController
{

    private $entityManager;
    private $playersRepository;
    private $crawlerService;
    private $playersOnlineRepository;
    private $playersOnlineHorasRepository;

	public function __construct(
        PlayersRepository $playersRepository, 
		EntityManagerInterface $entityManager,
        CrawlerService $crawlerService,
        PlayersOnlineRepository $playersOnlineRepository,
        PlayersOnlineHorasRepository $playersOnlineHorasRepository
	){
		$this->playersRepository = $playersRepository;
        $this->entityManager = $entityManager; 
        $this->crawlerService = $crawlerService;
        $this->playersOnlineRepository = $playersOnlineRepository;
        $this->playersOnlineHorasRepository = $playersOnlineHorasRepository;
	}    

    /**
     * @Route("/ec02c59dee6faaca3189bace969c22d3/online", name="ec02c59dee6faaca3189bace969c22d3_online")
     */
    public function online(): Response
    {
        $players = $this->playersOnlineRepository->findAll();

        $arrayPlayers = [];

        foreach($players as $p){
            $player = ['nome' => $p->getNome(), 'level' => $p->getLevel()];
            $arrayPlayers [] = $player;
        }

        return new JsonResponse(['players' => $arrayPlayers]); 
    }



}
