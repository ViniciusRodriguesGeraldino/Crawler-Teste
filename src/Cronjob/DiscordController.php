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
use App\Repository\PlayersGuildRepository;
use App\Entity\Players;
use App\Entity\PlayersDeaths;
use App\Entity\PlayersGuild;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

use function Discord\contains;

class DiscordController extends Command {

    protected static $defaultName = 'app:discord-bot';
    
    private $params;
    private $entityManager;
    private $playersRepository;
    private $playersDeathsRepository;
    private $crawlerService;
    private $guildMembers;

    public function __construct(
        ParameterBagInterface $params, 
        EntityManagerInterface $entityManager, 
        PlayersRepository $playersRepository,
        PlayersDeathsRepository $playersDeathsRepository,
        CrawlerService $crawlerService,
        PlayersGuildRepository $playersGuildRepository
    ) {
        $this->params = $params;
        $this->entityManager = $entityManager;
        $this->playersRepository = $playersRepository;
        $this->playersDeathsRepository = $playersDeathsRepository;
        $this->crawlerService = $crawlerService;
        $this->playersGuildRepository = $playersGuildRepository;

        parent::__construct();
    }

    protected function configure() {
        $this
        ->setDescription('Discord')
        ->setHelp('App Discord')
        ->addArgument('teste', InputArgument::OPTIONAL, 'Teste');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        
        dump('Iniciando');

        dump('carregando guild members');
        $this->guildMembers = $this->playersGuildRepository->findAll();
        dump('guild members carregados!');
        
        $discord = new Discord([
            'token' => 'OTU4MzY0NDE4NTQ1MTgwNzAy.YkMQag.1qjzsg31o3AMRQ20IyyUE0UyFuo',
        ]);
        
        $discord->on('ready', function (Discord $discord) {
            echo "Bot is ready!", PHP_EOL;

            foreach ($discord->guilds as $guild) {
                echo "Guild: {$guild->name} (" . $guild->members->count() . " membros)" . PHP_EOL;
                echo "Channels: " . PHP_EOL;
                foreach ($guild->channels as $channel) {
                    echo "\t\t{$channel->name}" . PHP_EOL;
                }
                echo "Membros: " . PHP_EOL;
                foreach ($guild->members as $member) {;
                    echo "\t\t{$member->user->username}" . PHP_EOL;
                }
            }            
        
            // Listen for messages.
            $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) {

                echo "USERNAME: {$message->author->username}, USER_ID: {$message->author->id}, MESSAGE: {$message->content}", PHP_EOL;

                if (!$this->entityManager->getConnection()->isConnected()) {
                    $this->entityManager->getConnection()->close(); // Close any previous connection as they are not active
                    $this->entityManager->getConnection()->connect(); // Get a fresh connection
                }

                $help = false;
                if(str_contains($message->content, "/help")){

                    echo "/help";
                    $message->channel->sendMessage("Comandos:\n Add enemy, nome_char, comentario \n Remove enemy, nome_char \n List enemys \n Player Info, nome_char, 25/04/2022");
                    $help = true;
                                                
                }

                $admin = false;
                if($message->author->id == '463476579969007636'){

                    $admin = true;

                }

                //ADMIN MSGS
                if($help == false && $admin == true){

                    if(str_contains(strtolower($message->content), "add enemy")){

                        $msg = explode(',', $message->content);

                        if(sizeof($msg) != 3){
                            $message->channel->sendMessage("comando invalido. (add enemy, nome, observacao)");    
                        }else{

                            $nome = trim($msg[1]);
                            $obs = $msg[2];

                            echo "Adicionando inimigo {$nome}";
                            $this->crawlerService->addPlayerEnemy($nome, $obs);
                            echo "Inimigo {$nome} adicionado";
                            $message->channel->sendMessage("{$nome} adicionado");
                    
                        }
                    }

                    if(str_contains(strtolower($message->content), "remove enemy")){


                        $msg = explode(',', $message->content);
                        
                        if(sizeof($msg) != 2){
                            $message->channel->sendMessage("comando invalido. (remove enemy, nome)");    
                        }else{

                            $nome = trim($msg[1]);

                            echo "Removendo inimigo {$nome}";
                            if($this->crawlerService->removePlayerEnemy($nome) == true){
                               echo "Inimigo {$nome} removido";
                            }else{
                                echo "Erro ao remover inimigo {$nome}!";
                            }
                            $message->channel->sendMessage("{$nome} removido");
                        }
                    }

                    if(str_contains(strtolower($message->content), "list enemy")){

                        echo "Listando todos inimigos";                    
                        $msg = $this->crawlerService->getListPlayersEnemys();
                        $aa = "```diff";
                        $aa .= "\n ALL ENEMYS";
                                
                        if(sizeof($msg) > 0){
                            for($i = 0; $i<=sizeof($msg)-1; $i++) {
                                $aa .= "\n +++ {$msg[$i]['nome']} ({$msg[$i]['obs']})";
                            }
                        }   
                        $aa .= "```";

                        $message->channel->sendMessage($aa);

                    }           
                    
                    if(str_contains(strtolower($message->content), "player info")){

                        $msg = explode(',', $message->content);

                        if(sizeof($msg) != 3){
                            $message->channel->sendMessage("comando invalido. (player info, nome, data)");    
                        }else{

                            $nome = trim($msg[1]);
                            $data2 = trim($msg[2]);
                            $data = \DateTime::createFromFormat('d/m/Y',$data2);
                            $data->modify('-1 day');
                            $data = $data->format('d/m/Y');
                            
                            if(sizeof($msg) < 2){
                                $message->channel->sendMessage("Erro ao carregar informações do player.");
                                return false;
                            }
                            
                            $aa = "```diff";
                            echo "Player Info {$nome} \n";

                            $totalHoras = $this->crawlerService->getPlayerHoras($nome, $data2);
                            
                            $aa .= "\n$nome";

                            if($totalHoras && sizeof($totalHoras) > 0){
                                $aa .= "\n Total de Horas em {$data2}: " . $totalHoras[0]['totalHoras'];
                            }

                            $totalHorasDetalhe = $this->crawlerService->getPlayerHorasDetalhado($nome, $data2);
                                    
                            if($totalHorasDetalhe && sizeof($totalHorasDetalhe) > 0){
                                $aa .= "\n Data        Online     Offline";
                                for($i = 0; $i<=sizeof($totalHorasDetalhe)-1; $i++) {
                                    $aa .= "\n {$totalHorasDetalhe[$i]['data']} - {$totalHorasDetalhe[$i]['hora']} ({$totalHorasDetalhe[$i]['offline']})";
                                }
                            } 
                                                    
                            if($data2 != ""){
                                $playerXp = $this->crawlerService->getPlayerExperiencia($nome, $data, $data2);
                                
                                if($playerXp && sizeof($playerXp) > 0){
                                    $aa .= "\n Experiencia {$data} até {$data2}";
                                    for($i = 0; $i<=sizeof($playerXp)-1; $i++) {
                                        $aa .= "\n LEVEL: {$playerXp[$i]['lvl1']} -> {$playerXp[$i]['lvl2']} | {$playerXp[$i]['exp2']} -> {$playerXp[$i]['exp1']} |Total: ({$playerXp[$i]['experiencia']})";
                                    }
                                } 
                            }
                            
                            $aa .= "```";

                            $message->channel->sendMessage($aa);  
                        }
                    }

                }//FIM ADMIN MSGS     
                
            });

            
            $discord->on('heartbeat', function () use ($discord) {

                if (!$this->entityManager->getConnection()->isConnected()) {
                    $this->entityManager->getConnection()->close(); // Close any previous connection as they are not active
                    $this->entityManager->getConnection()->connect(); // Get a fresh connection
                }
        
                echo "heartbeat called at: " . time() . PHP_EOL;
            });            
            
            
            $channel = $discord->getChannel(962573173004570755);   
            
        });

        //Apaga online a cada 55 segundos....
        $discord->getLoop()->addPeriodicTimer(55, function() use ($discord){

            $channel = $discord->getChannel(962573173004570755);   
            $channel->limitDelete(100)->done(function (Discord $discord) {

            });
           
        });

        //Envia Enemys Online
        $discord->getLoop()->addPeriodicTimer(60, function() use ($discord){

            $channel = $discord->getChannel(963992844639567883);   

            $msg = $this->crawlerService->getPlayersEnemys();
            
            $aa = "```diff";
            $aa .= "\n ENEMYS ONLINE";
                    
            if(sizeof($msg) > 0){
                for($i = 0; $i<=sizeof($msg)-1; $i++) {
                    $aa .= "\n +++ {$msg[$i]['nome']} ({$msg[$i]['obs']})";
                }
            }   
            $aa .= "```";

            if(sizeof($msg) > 0){
                $channel->sendMessage($aa);
            }
            
        });        

        //Envia Guild Online a cada 60 segundos...
        /*
        $discord->getLoop()->addPeriodicTimer(60, function() use ($discord){

            $channel = $discord->getChannel(963993941571678288);   

            $msg = $this->crawlerService->getPlayersGuildOnline();
            
            $aa = "```diff";
            $aa .= "\n GUILD ONLINE";
                    
            if(sizeof($msg) > 0){
                for($i = 0; $i<=sizeof($msg)-1; $i++) {
                    $aa .= "\n +++ {$msg[$i]['nome']}";
                }
            }   
            $aa .= "```";

            if(sizeof($msg) > 0){
                $channel->sendMessage($aa);
            }
            
        });*/

        $discord->run();

        dump('Finalizado');

        return 0;
    }

}