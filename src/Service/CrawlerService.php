<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Players;
use App\Entity\PlayersDeaths;
use App\Entity\PlayersEnemys;
use App\Entity\PlayersGuild;
use App\Entity\PlayersHighscore;
use App\Entity\PlayersTop10;
use App\Repository\PlayersDeathsRepository;
use App\Repository\PlayersGuildRepository;
use Symfony\Component\DomCrawler\Crawler;
use App\Repository\PlayersRepository;
use App\Repository\PlayersOnlineRepository;
use App\Repository\PlayersOnlineHorasRepository;
use App\Repository\PlayersEnemysRepository;
use App\Repository\PlayersHighscoreRepository;
use App\Repository\PlayersTop10Repository;

class CrawlerService {

    private $entityManager;
    private $logRepository;
    private $mailer;

    public function __construct(
        EntityManagerInterface $entityManager,
        PlayersRepository $playersRepository,
        PlayersDeathsRepository $playersDeathsRepository,
        PlayersOnlineRepository $playersOnlineRepository,
        PlayersGuildRepository $playersGuildRepository,
        PlayersEnemysRepository $playersEnemysRepository,
        PlayersOnlineHorasRepository $playersOnlineHorasRepository,
        PlayersHighscoreRepository $playersHighscoreRepository,
        PlayersTop10Repository $playersTop10Repository
    ) {
        $this->entityManager = $entityManager;
        $this->playersRepository = $playersRepository;
        $this->playersDeathsRepository = $playersDeathsRepository;
        $this->playersOnlineRepository = $playersOnlineRepository;
        $this->playersGuildRepository = $playersGuildRepository;
        $this->playersEnemysRepository = $playersEnemysRepository;
        $this->playersOnlineHorasRepository = $playersOnlineHorasRepository;
        $this->playersHighscoreRepository = $playersHighscoreRepository;
        $this->playersTop10Repository = $playersTop10Repository;
    }

    public function atualizaPlayer(Players $player, $url) {

        //$url = 'https://dura-online.com/?characters/Orion+Luca';
        $html = $this->getHtml($url);
        
        $crawler = new Crawler($html);

        if($crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[1]/tr[2]/td[2]/span/b')->count() == 0){
            return false;
        }

        $nome = $crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[1]/tr[2]/td[2]/span/b')->text();
        
        if(!$player){
            $player = $this->playersRepository->findOneBy(['nome' => $nome]);
        }

        if(!$player){        
            return false;
        }        

        
        $accCriadaEm = "";
        if($crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[3]/tr[1]/td/b')->count() > 0){
            if($crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[3]/tr[1]/td/b')->text() == 'Account Information'){
                $accCriadaEm = $crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[3]/tr[2]/td[2]')->text();
            }
        }
                                  
        $nomeGuild = "";          
        if($crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[1]/tr[7]/td[1]')->count() > 0){
            if($crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[1]/tr[7]/td[1]')->text() == 'Guild membership:'){
                $nomeGuild = $crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[1]/tr[7]/td[2]')->text();
            }
        }        
        
        $linkGuild = "";
        if($crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[1]/tr[7]/td[2]/a/@href')->count() > 0){
            $linkGuild = $crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[1]/tr[7]/td[2]/a/@href')->text();
        }

        $dataUltimoLogin = $crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[1]/tr[8]/td[2]')->text();  
        $dataCriacao = $crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[1]/tr[9]/td[2]')->text();  
        
        
        $dataUltimoLogin = \DateTime::createFromFormat('M d Y, H:i:s e', $dataUltimoLogin);
        $dataCriacao = \DateTime::createFromFormat('M d Y, H:i:s e', $dataCriacao);
        if(!$dataCriacao){
            $dataCriacao = new \DateTime();
        }

        
        //dd($accCriadaEm);
        if($accCriadaEm){
            $accCriadaEm = \DateTime::createFromFormat('d F Y, H:i a', $accCriadaEm);
        }
        //dd($accCriadaEm);
        
        
        $player->setNomeGuild($nomeGuild);
        $player->setLinkGuild($linkGuild);        
        $player->setDataUltimoLogin($dataUltimoLogin);
        $player->setDataCriacao($dataCriacao);
        $player->setDataConsulta(new \DateTime());
        
        if($accCriadaEm){
            $player->setDataAccCriada($accCriadaEm);
        }

        $this->entityManager->persist($player);
        $this->entityManager->flush();

    }

    public function atualizaDeathsPlayer(Players $player, $url){

        $html = $this->getHtml($url);
        
        $crawler = new Crawler($html);

        if($crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[1]/tr[2]/td[2]/span/b')->count() == 0){
            return false;
        }

        $nome = $crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[1]/tr[2]/td[2]/span/b')->text();

        if(!$player){
            $player = $this->playersRepository->findOneBy(['nome' => $nome]);
        }

        if(!$player){        
            return false;
        }
        $mortes = [];
                                        
        //dd($crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[2]/tr[1]/td/b')->text());
              
        $table = 2;
        if($crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[2]/tr[1]/td/b')->count() > 0){
            $temHouse = $crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table[2]/tr[1]/td/b')->text();
            
            //if($url == "https://dura-online.com/?characters/Nonaban"){
            //    dd($temHouse);
            //}

            if($temHouse == "Rented Houses"){
                $table = 3;
            }

            if($temHouse == "Account Information"){
                return false;
            }

        }
        $qtdMortes = $crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table['.$table.']/tr')->count();

        
        
        for($i = 2; $i<=$qtdMortes; $i++) {
            
            $dataMorte = $crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table['.$table.']/tr['.$i.']/td[1]')->text();
            $dataMorte = \DateTime::createFromFormat('d M Y, H:i', $dataMorte);
            if(!$dataMorte){
                return false;
            }

            $killedBy = $crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table['.$table.']/tr['.$i.']/td[2]')->text();

            $linkKilledBy = "";
            if($crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table['.$table.']/tr['.$i.']/td[2]/a/@href')->count() > 0){
                $linkKilledBy = $crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table['.$table.']/tr['.$i.']/td[2]/a/@href')->text();
            }

            $linkKilledByTwo = "";
            if($crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table['.$table.']/tr['.$i.']/td[2]/a[2]/@href')->count() > 0){
                $linkKilledByTwo = $crawler->filterXPath('//*[@id="characters"]/div[5]/div/div/table/tr/td[2]/table['.$table.']/tr['.$i.']/td[2]/a[2]/@href')->text();  
            }

            $mortes[] = ['nome' => $nome, 'dataMorte' => $dataMorte, 'killedBy' => $killedBy, 
                         'linkKilledBy' => $linkKilledBy, 'linkKilledByTwo' => $linkKilledByTwo];


            
            
            $death = $this->playersDeathsRepository->findOneBy(['player' => $player, 'dataMorte' => $dataMorte]);
            if(!$death){
                $death = new PlayersDeaths();
                $death->setPlayer($player);
                $death->setNome($nome);
                $death->setDataMorte($dataMorte);
                $death->setKilledBy($killedBy);
                $death->setLinkKilledBy($linkKilledBy);
                $death->setLinkKilledByTwo($linkKilledByTwo);
                $this->entityManager->persist($death);
                $this->entityManager->flush();   
            }
        }      

        
        return $mortes;
        
    }

    public function getHtml($url){
        
        //return file_get_contents($url);
        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, Array('Content-Type: application/x-www-form-urlencoded; charset=ISO-8859-1'));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $resp = curl_exec($curl);
        curl_close($curl);
        
        return $resp;
    }


    public function getPlayersOnline(){

        $players = $this->playersOnlineRepository->findAll();

        $arrayPlayers = [];
        $arrayGuildMembers = [];
        $arrayEnemys = [];

        foreach($players as $p){

            $playerCadastro = $this->playersRepository->findOneBy(['nome' => $p->getNome()]);
    
            if(!$playerCadastro){        
                continue;
            }            

            $player = ['nome' => $p->getNome()];

            if($playerCadastro->getNomeGuild() && str_contains($playerCadastro->getNomeGuild(), 'Dragon Order')){
                $arrayGuildMembers [] = $player;
            }else if ($this->isEnemy($playerCadastro->getNome())) {
                $arrayEnemys [] = $player;
            }else{                          
                $arrayPlayers [] = $player;
            }
        }

        $msg = "ONLINE ";

        if(sizeof($arrayGuildMembers) > 0){
            $msg .= 'Guild Online \n';

            for($i = 0; $i<=sizeof($arrayGuildMembers)-1; $i++) {
                $msg .= $arrayGuildMembers[$i]["nome"];
            }
        }

        if(sizeof($arrayEnemys) > 0){
            $msg .= 'Enemys Online \n';

            for($i = 0; $i<=sizeof($arrayEnemys)-1; $i++) {
                $msg .= $arrayEnemys[$i]["nome"];
            }
        }    
        
        if(sizeof($arrayPlayers) > 0){
            $msg .= 'Neutros \n';

            for($i = 0; $i<=sizeof($arrayPlayers)-1; $i++) {
                $msg .= $arrayPlayers[$i]["nome"];
            }
        }            

        return  $msg;

    }

    public function isEnemy($nome){
        
        $isEnemy = $this->playersEnemysRepository->findOneBy(['nome' => $nome]);

        if($isEnemy){
            return true;
        }else{
            return false;
        }

    }

    public function getPlayersOnline2(){
        return $this->playersOnlineRepository->findAll();
    }
    
    public function getPlayersGuild(){
        return $this->playersGuildRepository->findAll();
    }

    public function  getPlayersGuildOnline(){
        $guildMembers = $this->playersGuildRepository->findAll();

        $arrayMembers = [];

        foreach($guildMembers as $g){
            $nome = $g->getNome();
            $p = $this->playersOnlineRepository->findOneBy(['nome' => $nome]);
            if($p){
                $arrayMembers[] = ['nome' => $nome];                
            }
        }

        return $arrayMembers;

    }

    public function getPlayersEnemys(){
        
        $enemys = $this->playersEnemysRepository->findAll();

        $arrayEnemys = [];

        foreach($enemys as $e){
            $nome = $e->getNome();
            $p = $this->playersOnlineRepository->findOneBy(['nome' => $nome]);
            if($p){
                $arrayEnemys[] = ['nome' => $nome, 'obs' => $e->getObservacao()];
            }
        }

        return $arrayEnemys;
    }

    public function getListPlayersEnemys(){
        
        $enemys = $this->playersEnemysRepository->findAll();

        $arrayEnemys = [];

        foreach($enemys as $e){
            $nome = $e->getNome();
            $arrayEnemys[] = ['nome' => $nome, 'obs' => $e->getObservacao()];
        }

        return $arrayEnemys;
    }    
    
    public function addPlayerEnemy($nome, $obs){
        
        $player = $this->playersEnemysRepository->findOneBy(['nome' => $nome]);
        if($player){
            return false;
        }

        $player = new PlayersEnemys();
        $player->setNome($nome);
        $player->setObservacao($obs);

        $this->entityManager->persist($player);
        $this->entityManager->flush();
    }

    public function removePlayerEnemy($nome){
        
        $player = $this->playersEnemysRepository->findOneBy(['nome' => $nome]);

        if($player){
            
            $this->entityManager->remove($player);
            $this->entityManager->flush();
            return true;
        }else{
            return false;
        }

           
    }

    public function crawlerGuild(){

        $playersGuild = $this->playersGuildRepository->findAll();
        
        foreach ($playersGuild as $pg) {
            $this->entityManager->remove($pg);
        }
        $this->entityManager->flush();

        $html = $this->getHtml('https://dura-online.com/?guilds/Dragon+Order');
        
        $crawler = new Crawler($html);
        
        //dd($crawler->filterXPath('//*[@id="guilds"]/div[5]/div/div/div/div/div/div[1]/table/tbody/tr/td/div/table/tbody/tr/td/div/div/table/tbody/tr[3]/td[1]')->text());
        //dd($crawler->filterXPath('//*[@id="guilds"]/div[5]/div/div/div/div/div/div[1]/table/tbody/tr/td/div/table/tbody/tr/td/div/div/table/tbody/tr[2]')->text());
                                               
        $qtdPlayers = $crawler->filterXPath('//*[@id="guilds"]/div[5]/div/div/div/div/div/div[1]/table/tbody/tr/td/div/table/tbody/tr/td/div/div/table/tbody/tr')->count();

        for($i = 2; $i<=$qtdPlayers; $i++) {

            $nome = $crawler->filterXPath('//*[@id="guilds"]/div[5]/div/div/div/div/div/div[1]/table/tbody/tr/td/div/table/tbody/tr/td/div/div/table/tbody/tr['.$i.']/td[2]')->text();
            $level = $crawler->filterXPath('//*[@id="guilds"]/div[5]/div/div/div/div/div/div[1]/table/tbody/tr/td/div/table/tbody/tr/td/div/div/table/tbody/tr['.$i.']/td[4]')->text();
    
            if($crawler->filterXPath('//*[@id="guilds"]/div[5]/div/div/div/div/div/div[1]/table/tbody/tr/td/div/table/tbody/tr/td/div/div/table/tbody/tr['.$i.']/td[1]')->text() != ""){
                $rank = $crawler->filterXPath('//*[@id="guilds"]/div[5]/div/div/div/div/div/div[1]/table/tbody/tr/td/div/table/tbody/tr/td/div/div/table/tbody/tr['.$i.']/td[1]')->text();
            }

            $playerGuild = new PlayersGuild();
            $playerGuild->setNome($nome);
            $playerGuild->setNomeRank($rank);
            $playerGuild->setLevel($level);
            
            $this->entityManager->persist($playerGuild);
        }
        
        $this->entityManager->flush();

        return $this->playersGuildRepository->findAll();

    }


    public function getPlayerHoras($nome, $data){
       
        $player = $this->playersRepository->findOneBy(['nome' => $nome]);

        if(!$player){
            return false;
        }

        $dadosPlayer = $this->playersOnlineHorasRepository->getHoras($player->getNome(), $data);

        $arrayDados = [];

        foreach($dadosPlayer as $e){
            $arrayDados[] = ['nome' => $nome, 'data' => $data, 'totalHoras' => $e['total']];
        }

        return $arrayDados;
    }   
    
    public function getPlayerHorasDetalhado($nome, $data){
       
        $player = $this->playersRepository->findOneBy(['nome' => $nome]);

        if(!$player){
            return false;
        }

        $dadosPlayer = $this->playersOnlineHorasRepository->getPlayerHorasDetalhado($player->getNome(), $data);

        $arrayDados = [];

        foreach($dadosPlayer as $e){
            $arrayDados[] = ['nome' => $nome, 'data' => $data, 'hora' => $e['hora_online'], 'offline' => $e['hora_offline']];
        }

        return $arrayDados;
    }       

    public function getPlayerHorasPeriodo($nome, $data1, $data2){
       
        $player = $this->playersRepository->findOneBy(['nome' => $nome]);

        if(!$player){
            return false;
        }

        $dadosPlayer = $this->playersOnlineHorasRepository->getPlayerHorasPeriodo($player->getNome(), $data1, $data2);

        $arrayDados = [];

        foreach($dadosPlayer as $e){
            $arrayDados[] = ['nome' => $nome, 'periodo' => $data1 . ' a ' . $data2, 'totalHoras' => $e['total']];
        }

        return $arrayDados;
    }          
    
    public function getPlayerHorasAccCriada(){
       
        $dadosPlayer = $this->playersOnlineHorasRepository->getAccCriada();

        $arrayDados = [];

        foreach($dadosPlayer as $e){
            $arrayDados[] = ['nome' => $e['nome'], 'data_criada' => $e['data_acc_criada']];
        }

        return $arrayDados;
    }       

    public function getPlayerExperiencia($nome, $data1, $data2){

        $dadosPlayer = $this->playersOnlineHorasRepository->getExperienciaPlayer($nome, $data1, $data2);

        $arrayDados = [];

        foreach($dadosPlayer as $e){
            $arrayDados[] = ['nome' => $e['nome'], 'lvl1' => $e['lvl1'], 'lvl2' => $e['lvl2'], 'exp1' => $e['exp1'], 'exp2' => $e['exp2'], 'experiencia' => $e['experiencia']];
        }

        return $arrayDados;        

    }

    public function getExperienciaPlayerTop10(){

        $data1 = date("Y-m-d", strtotime("yesterday")); 
        $data2 = date("Y-m-d", strtotime("now")); 
        

        $dadosPlayer = $this->playersOnlineHorasRepository->getExperienciaPlayerTop10($data1, $data2);

        $playersTop10 = $this->playersTop10Repository->findBy(['data' => new \DateTime()]);
        

        foreach ($playersTop10 as $p) {
            $this->entityManager->remove($p);
        }

        foreach($dadosPlayer as $e){

            $top10 = new PlayersTop10();
            $top10->setData(new \DateTime());
            $top10->setNome($e['nome']);
            $top10->setLvl1($e['lvl1']);
            $top10->setLvl2($e['lvl2']);
            $top10->setExp1($e['exp1']);
            $top10->setExp2($e['exp2']);
            $top10->setExperiencia($e['experiencia']);

            $this->entityManager->persist($top10);
        }

        $this->entityManager->flush();

        return true;
    }    


    public function crawlerHighscore($pagina){

        $data = new \DateTime();

        //$jaFezHoje = $this->playersHighscoreRepository->findOneBy(['data' => $data]);
        //if($jaFezHoje){
            //return 'jafez';
        //}
      
        $html = $this->getHtml('https://dura-online.com/?highscores/experience/'.$pagina);
        
        $crawler = new Crawler($html);
        
        //dd($crawler->filterXPath('//*[@id="highscores"]/div[5]/div/div/table/tr/td[2]/table[2]/tr')->count());
        //dd($crawler->filterXPath('//*[@id="highscores"]/div[5]/div/div/table/tr/td[2]/table[2]/tr[9]/td[1]')->text());
                                            
        
        $qtdPlayers = $crawler->filterXPath('//*[@id="highscores"]/div[5]/div/div/table/tr/td[2]/table[2]/tr')->count();
        

        for($i = 3; $i<=$qtdPlayers; $i++) {

            $rank = $crawler->filterXPath('//*[@id="highscores"]/div[5]/div/div/table/tr/td[2]/table[2]/tr['.$i.']/td[1]')->text();
            
            $nome = $crawler->filterXPath('//*[@id="highscores"]/div[5]/div/div/table/tr/td[2]/table[2]/tr['.$i.']/td[2]/a')->text();
            
            $level = $crawler->filterXPath('//*[@id="highscores"]/div[5]/div/div/table/tr/td[2]/table[2]/tr['.$i.']/td[3]/div')->text();
            
            $experiencia = $crawler->filterXPath('//*[@id="highscores"]/div[5]/div/div/table/tr/td[2]/table[2]/tr['.$i.']/td[4]/div')->text();
            $experiencia = preg_replace("/[^0-9]/", "", $experiencia);

            $playerH = new PlayersHighscore();
            $playerH->setData($data);
            $playerH->setNome($nome);
            $playerH->setPosicaoRank($rank);
            $playerH->setLevel($level);
            $playerH->setExperiencia($experiencia);
            
            $this->entityManager->persist($playerH);
        }

        $this->entityManager->flush();

        return 'ok';

    }  

}

