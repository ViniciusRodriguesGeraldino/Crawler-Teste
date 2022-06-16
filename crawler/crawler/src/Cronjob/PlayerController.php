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

class PlayerController extends Command {

    protected static $defaultName = 'app:player-controller';
    
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
        CrawlerService $crawlerService
    ) {
        $this->params = $params;
        $this->entityManager = $entityManager;
        $this->playersRepository = $playersRepository;
        $this->playersDeathsRepository = $playersDeathsRepository;
        $this->crawlerService = $crawlerService;
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

        $nome = $input->getArgument('character');

        if($nome){
            $player = $this->playersRepository->findOneBy(['nome' => $nome]);

            if(!$player){        
                return 0;
            }            
            
            dump('Lendo dados do player ' . $nome);

            $this->crawlerService->atualizaPlayer($player, $player->getLink());
            //$this->crawlerService->atualizaDeathsPlayer($player, $player->getLink());            

        }else{

            $players = $this->playersRepository->getPlayers();

            if ($players) {
                
                foreach ($players as $player) {
                    dump('Lendo dados do player ' . $player->getNome());
                    $this->crawlerService->atualizaPlayer($player, $player->getLink());
                    //$this->crawlerService->atualizaDeathsPlayer($player, $player->getLink());
                }
    
            }    

        }

        dump('Finalizado');

        return 0;
    }

}