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


class Top10Controller extends Command {

    protected static $defaultName = 'app:top10';
    
    private $params;
    private $entityManager;
    private $crawlerService;

    public function __construct(
        ParameterBagInterface $params, 
        EntityManagerInterface $entityManager, 
        CrawlerService $crawlerService
    ) {
        $this->params = $params;
        $this->entityManager = $entityManager;
        $this->crawlerService = $crawlerService;
        parent::__construct();
    }

    protected function configure() {
        $this
        ->setDescription('Top 20')
        ->setHelp('carrega diariamente o top 20');        
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        
        dump('Iniciando');        
        $this->crawlerService->getExperienciaPlayerTop10();        
        dump('Finalizado');

        return 0;
    }

}