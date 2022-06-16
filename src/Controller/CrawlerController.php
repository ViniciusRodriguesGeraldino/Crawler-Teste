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
* @Route("/",name="homepage")
*/
class CrawlerController extends AbstractController
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
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('home/index.html.twig', [
            'error'         => '',
        ]);

    }  

    /**
     * @Route("/items", name="items_seller")
     */
    public function itemsSell(): Response
    {
        return $this->render('home/items.html.twig', [
            'error'         => '',
        ]);

    }  
    
    /**
     * @Route("/quests", name="quests")
     */
    public function quests(): Response
    {
        return $this->render('home/index.html.twig', [
            'error'         => '',
        ]);

    }  
    
    /**
     * @Route("/legendary", name="legendary")
     */
    public function worksOnline(): Response
    {
        return $this->render('home/index.html.twig', [
            'error'         => '',
        ]);

    }      


    
    /**
     * @Route("/89ffb49f6c28afaac0c29af9c9d208ac/crawler", name="89ffb49f6c28afaac0c29af9c9d208ac_crawler")
     */
    public function indexOnline(): Response
    { 
		$session = $this->get('session');

        $html = $this->crawlerService->getHtml('https://dura-online.com/?online');
        
        $players = [];

        $crawler = new Crawler($html);

        $qtdOnline = $crawler->filterXPath('//*[@id="online"]/div[5]/div/div/table[2]/tr')->count();
        for($i = 2; $i<=$qtdOnline; $i++) {
            $nome = $crawler->filterXPath('//*[@id="online"]/div[5]/div/div/table[2]/tr['.$i.']/td[1]')->text();
            $level = $crawler->filterXPath('//*[@id="online"]/div[5]/div/div/table[2]/tr['.$i.']/td[2]')->text();
            $vocacao = $crawler->filterXPath('//*[@id="online"]/div[5]/div/div/table[2]/tr['.$i.']/td[3]')->text();
            $link = $crawler->filterXPath('//*[@id="online"]/div[5]/div/div/table[2]/tr['.$i.']/td[1]/span/b/a/@href')->text();

            $players[] = ['nome' => $nome, 'level' => $level, 'vocacao' => $vocacao, 'link' => $link];
        }
        
        //dd($html = $crawler->html());    

       $this->salvaPlayers($players);
       return new JsonResponse($players);
    }

    public function salvaPlayers($players) { 
		
        for ($i = 0; $i < count($players); $i++) {
            
            $nome = $players[$i]["nome"];

            $player = $this->playersRepository->findOneBy(['nome' => $nome]);
            if(!$player){
                $player = new Players();
                $player->setNome($nome);
            }
            
            $player->setLevel($players[$i]["level"]);
            $player->setVocacao($players[$i]["vocacao"]);
            $player->setLink($players[$i]["link"]);
            $player->setDataConsulta(new \DateTime());

            $this->entityManager->persist($player);
            $this->entityManager->flush();

        }
        
    }  
    
    /**
     * @Route("/9e5317e838cb5bd8e98a013fffc2b30e/character_teste", name="9e5317e838cb5bd8e98a013fffc2b30e_character_teste")
     */
    public function testeCharacter()
    { 
        
        $player = $this->playersRepository->findOneBy(['nome' => "Lex Luthor"]);

        if ($player) {           
            
            $this->crawlerService->atualizaPlayer($player, $player->getLink());        

        }

       return new JsonResponse('ok');
    }        
    

    /**
     * @Route("/9e5317e838cb5bd8e98a013fffc2b30e/character", name="9e5317e838cb5bd8e98a013fffc2b30e_character")
     */
    public function indexCharacter()
    { 
        
        $players = $this->playersRepository->getPlayers();

        if ($players) {
            
            foreach ($players as $player) {
                $this->crawlerService->atualizaPlayer($player, $player->getLink());
            }

        }

       return new JsonResponse('ok');
    }    


    /**
     * @Route("/934b535800b1cba8f96a5d72f72f1611/character_death", name="934b535800b1cba8f96a5d72f72f1611_character_death")
     */
    public function indexCharacterDeaths()
    { 
        
        $players = $this->playersRepository->getPlayers();

        if ($players) {
            
            foreach ($players as $player) {
                $this->crawlerService->atualizaDeathsPlayer($player, $player->getLink());
            }

        }
        //$player = new Players();
        //$ret = $this->crawlerService->atualizaDeathsPlayer($player, "https://dura-online.com/?characters/Frost");

       return new JsonResponse("ok");
    }        


    /**
     * @Route("/b0baee9d279d34fa1dfd71aadb908c3f/playersonline", name="b0baee9d279d34fa1dfd71aadb908c3f_playersonline")
     */
    public function playersOnline(): Response
    { 
		//$session = $this->get('session');

        $playersOnline = $this->playersOnlineRepository->findAll();
        
        foreach ($playersOnline as $pp) {
            $this->entityManager->remove($pp);
        }
        $this->entityManager->flush();

        $html = $this->crawlerService->getHtml('https://dura-online.com/?online');
        
        $crawler = new Crawler($html);
                                               
        $qtdOnline = $crawler->filterXPath('//*[@id="online"]/div[5]/div/div/table[2]/tr')->count();

        for($i = 2; $i<=$qtdOnline; $i++) {

            $nome = $crawler->filterXPath('//*[@id="online"]/div[5]/div/div/table[2]/tr['.$i.']/td[1]')->text();
            $level = $crawler->filterXPath('//*[@id="online"]/div[5]/div/div/table[2]/tr['.$i.']/td[2]')->text();

            $playerOnline = new PlayersOnline();
            $playerOnline->setNome($nome);
            $playerOnline->setDataConsulta(new \DateTime());
            $playerOnline->setLevel($level);
            
            $this->entityManager->persist($playerOnline);
            $this->entityManager->flush();            

        }
       
        $this->atualizaPlayersDiarioOnline();
        $this->atualizaPlayersDiarioOffline();        
        

       return new JsonResponse('ok');
    }    
    
    //grava na tabela para estatisticas de quando o player ta on.
    public function atualizaPlayersDiarioOnline(){

        $playersOnline = $this->playersOnlineRepository->findAll();
        
        foreach($playersOnline as $player){

            $nome = $player->getNome();
            $level = $player->getLevel();
            
            $playerHora = $this->playersOnlineHorasRepository->findOneBy(['nome' => $nome, 'dataOnline' => new \DateTime(), 'horaOffline' => null]);

            if(!$playerHora){
                
                $playerHora = new PlayersOnlineHoras();
                $playerHora->setNome($nome);   
                $playerHora->setDataOnline(new \Datetime());
                $playerHora->setHoraOnline(new \DateTime());
                $playerHora->setLevel($level);

                $this->entityManager->persist($playerHora);
                $this->entityManager->flush();        

            }

        }

    }

    public function atualizaPlayersDiarioOffline(){

        $players = $this->playersOnlineHorasRepository->findBy(['horaOffline' => null]);

        if (sizeof($players) > 0) {
         
            foreach($players as $p){
                
                //dd($this->playersOnlineRepository);
                $playerOnlineAgora = $this->playersOnlineRepository->findOneBy(['nome' => $p->getNome()]);
                

                if(!$playerOnlineAgora){
                    $p->setHoraOffline(new \DateTime());
                    $this->entityManager->persist($p);
                    $this->entityManager->flush();     
                }

            }

        }
    }


}
