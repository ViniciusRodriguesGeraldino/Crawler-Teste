<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Players;
use App\Entity\PlayersDeaths;
use App\Repository\PlayersDeathsRepository;
use Symfony\Component\DomCrawler\Crawler;
use App\Repository\PlayersRepository;

class CrawlerService {

    private $entityManager;
    private $logRepository;
    private $mailer;

    public function __construct(
        EntityManagerInterface $entityManager,
        PlayersRepository $playersRepository,
        PlayersDeathsRepository $playersDeathsRepository

    ) {
        $this->entityManager = $entityManager;
        $this->playersRepository = $playersRepository;
        $this->playersDeathsRepository = $playersDeathsRepository;
    }

    public function atualizaPlayer(Players $player, $url) {

        //$url = 'https://dura-online.com/?characters/Orion+Luca';
        $html = $this->getHtml($url);
        
        $crawler = new Crawler($html);

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

}

