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
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Notifier\Bridge\Discord\DiscordOptions;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordEmbed;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordFieldEmbedObject;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordFooterEmbedObject;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordMediaEmbedObject;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\ChatterInterface;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;


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
        $players = $this->crawlerService->getPlayersOnline();

        return new JsonResponse($players); 
    }

    /**
     * @Route("/ec02c59dee6faaca3189bace969c22d3/guild", name="ec02c59dee6faaca3189bace969c22d3_guild")
     */
    public function guild(): Response
    {
        //$players = $this->crawlerService->getPlayersGuild();
        $players = $this->crawlerService->crawlerGuild();

        return new JsonResponse($players); 
    }

    
    /**
     * @Route("/c2bd33f21e941798d0570901e777383a/addEnemy", name="c2bd33f21e941798d0570901e777383a_addEnemy")
     */
    public function addEnemy(Request $request): Response
    {
        /*$nome = $request->get('nome');
        $obs = $request->get('obs');
        $this->crawlerService->addPlayerEnemy($nome, $obs);*/

        return new JsonResponse('ok'); 
    }    

    /**
     * @Route("/9eac0362fb0afdffbdf76bc4e932d158/removeEnemy", name="9eac0362fb0afdffbdf76bc4e932d158_removeEnemy")
     */
    public function removeEnemy(Request $request): Response
    {
        /*$nome = $request->get('nome');
        $this->crawlerService->removePlayerEnemy($nome);*/

        return new JsonResponse('ok'); 
    }    
    
    /**
     * @Route("/ec02c59dee6faaca36780bace969551d3/teste", name="ec02c59dee6faaca36780bace969551d3teste")
     */
    public function teste()
    {
        //$horas = $this->crawlerService->getPlayerHoras('Lex Luthor', '24/04/2022');
        //return new JsonResponse($horas[0]['totalHoras']);  

        //$horas = $this->crawlerService->getPlayerHorasDetalhado('Lex Luthor', '20/04/2022');
        //return new JsonResponse($horas);         

        //$horas = $this->crawlerService->getPlayerHorasPeriodo('Lex Luthor', '18/04/2022', '22/04/2022');
        //return new JsonResponse($horas);  
        
        //$horas = $this->crawlerService->getPlayerHorasAccCriada();
        //return new JsonResponse($horas);          
        
        //$horas = $this->crawlerService->getPlayerExperiencia('Lex Luthor', '20/04/2022', '22/04/2022');
        //return new JsonResponse($horas);  
        
        //$horas = $this->crawlerService->getExperienciaPlayerTop10();
        //return new JsonResponse($horas);     
    }    


}
