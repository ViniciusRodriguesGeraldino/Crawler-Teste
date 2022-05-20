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
use App\Repository\PlayersHighscoreRepository;


class HighscoreController extends Command {

    protected static $defaultName = 'app:highscore-controller';
    
    private $params;
    private $entityManager;
    private $playersRepository;
    private $playersDeathsRepository;
    private $crawlerService;

    public function __construct(
        ParameterBagInterface $params, 
        EntityManagerInterface $entityManager, 
        PlayersHighscoreRepository $playersHighscoreRepository,
        CrawlerService $crawlerService
    ) {
        $this->params = $params;
        $this->entityManager = $entityManager;
        $this->playersHighscoreRepository = $playersHighscoreRepository;
        $this->crawlerService = $crawlerService;
        parent::__construct();
    }

    protected function configure() {
        $this
        ->setDescription('Crawler Highscore')
        ->setHelp('Crawler Conjob');        
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        
        dump('Iniciando');
        
        for($i = 0; $i<=200; $i++) {
            dump('Lendo pagina ' .$i);
            $this->crawlerService->crawlerHighscore($i);
            dump('pagina lida');
        }
        dump('Finalizado');

        return 0;
    }

}