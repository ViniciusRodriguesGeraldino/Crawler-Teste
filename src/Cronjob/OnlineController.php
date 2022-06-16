<?php 

namespace App\Cronjob;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\CrawlerService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Repository\PlayersRepository;
use App\Repository\PlayersDeathsRepository;
use App\Entity\Players;
use App\Entity\PlayersDeaths;
use Symfony\Component\DomCrawler\Crawler;
use App\Entity\PlayersOnline;
use App\Entity\PlayersOnlineHoras;
use App\Repository\PlayersOnlineHorasRepository;
use App\Repository\PlayersOnlineRepository;

class OnlineController extends Command {

    protected static $defaultName = 'app:online-controller';
    
    private $params;
    private $entityManager;
    private $playersRepository;
    private $playersDeathsRepository;
    private $crawlerService;

    public function __construct(
        ParameterBagInterface $params, 
        EntityManagerInterface $entityManager, 
        PlayersRepository $playersRepository,
        PlayersDeathsRepository $playersDeathsRepository,
        CrawlerService $crawlerService,
        PlayersOnlineRepository $playersOnlineRepository,
        PlayersOnlineHorasRepository $playersOnlineHorasRepository
    ) {
        $this->params = $params;
        $this->entityManager = $entityManager;
        $this->playersRepository = $playersRepository;
        $this->playersDeathsRepository = $playersDeathsRepository;
        $this->crawlerService = $crawlerService;
        $this->playersOnlineRepository = $playersOnlineRepository;
        $this->playersOnlineHorasRepository = $playersOnlineHorasRepository;
        parent::__construct();
    }

    protected function configure() {
        $this
        ->setDescription('Crawler')
        ->setHelp('Crawler Conjob')
        ->addArgument('character', InputArgument::OPTIONAL, 'Nome do Char')
        ->addArgument('force', InputArgument::OPTIONAL, 'Forçar');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        
        dump('Iniciando');
        
        if(!$output){
            $output = New OutputInterface();
        }        

        $playersOnline = $this->playersOnlineRepository->findAll();
        
        dump('limpando tabela online.');
        foreach ($playersOnline as $pp) {
            $this->entityManager->remove($pp);
        }
        $this->entityManager->flush();
        dump('tabela limpa!');

        dump('Crawling tabela online...');
        $html = $this->crawlerService->getHtml('https://dura-online.com/?online');
	dump($html);        
        $crawler = new Crawler($html);
                                               
        $qtdOnline = $crawler->filterXPath('//*[@id="online"]/div[5]/div/div/table[2]/tr')->count();

	dump($qtdOnline);

        for($i = 2; $i<=$qtdOnline; $i++) {

            $nome = $crawler->filterXPath('//*[@id="online"]/div[5]/div/div/table[2]/tr['.$i.']/td[1]')->text();
            $level = $crawler->filterXPath('//*[@id="online"]/div[5]/div/div/table[2]/tr['.$i.']/td[2]')->text();

            dump('crawler player ' . $nome);

            $playerOnline = new PlayersOnline();
            $playerOnline->setNome($nome);
            $playerOnline->setDataConsulta(new \DateTime());
            $playerOnline->setLevel($level);
            
            $this->entityManager->persist($playerOnline);
        }
        
        $this->entityManager->flush();
       
        $this->atualizaPlayersDiarioOnline();
        $this->atualizaPlayersDiarioOffline();

        dump('Finalizado');

        return 0;
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
