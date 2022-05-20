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
use App\Repository\PlayersGuildRepository;

class GuildController extends Command {

    protected static $defaultName = 'app:guild-controller';
    
    private $params;
    private $entityManager;
    private $playersRepository;
    private $playersDeathsRepository;
    private $crawlerService;

    public function __construct(
        ParameterBagInterface $params, 
        EntityManagerInterface $entityManager, 
        PlayersGuildRepository $playersGuildRepository,
        CrawlerService $crawlerService
    ) {
        $this->params = $params;
        $this->entityManager = $entityManager;
        $this->playersGuildRepository = $playersGuildRepository;
        $this->crawlerService = $crawlerService;
        parent::__construct();
    }

    protected function configure() {
        $this
        ->setDescription('Crawler Guild')
        ->setHelp('Crawler Guild Conjob')
        ->addArgument('character', InputArgument::OPTIONAL, 'Nome do Char')
        ->addArgument('force', InputArgument::OPTIONAL, 'Forçar');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        
        dump('Iniciando Crawler Guild');
        
        if(!$output){
            $output = New OutputInterface();
        }        

        $this->crawlerService->crawlerGuild();

        dump('Crawler Finalizado');

        return 0;
    }

}