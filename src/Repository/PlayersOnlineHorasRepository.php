<?php

namespace App\Repository;

use App\Entity\PlayersOnlineHoras;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method PlayersOnlineHoras|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayersOnlineHoras|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayersOnlineHoras[]    findAll()
 * @method PlayersOnlineHoras[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayersOnlineHorasRepository extends ServiceEntityRepository
{
    private $entityManager;
    
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($registry, PlayersOnlineHoras::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(PlayersOnlineHoras $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(PlayersOnlineHoras $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getHoras($nome, $data){

        $query = "select p.nome, p.data_online, 
                    sec_to_time(sum(time_to_sec(if(TIMEDIFF(p.hora_offline, p.hora_online) < 0, SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF(STR_TO_DATE('23:59:59', '%k:%i:%S'), p.hora_online)) + TIME_TO_SEC(TIMEDIFF(p.hora_offline, STR_TO_DATE('00:00:01', '%k:%i:%S')))), 
                    TIMEDIFF(p.hora_offline, p.hora_online))))) 
                    as 'total' 
                    from players_online_horas p
                    where p.nome = :nome
                    and p.data_online = :data
                    group by nome, data_online ";

        $ymd = \DateTime::createFromFormat('d/m/Y', $data);
        $ymd = ($ymd)->format('Y-m-d');
        //dd($ymd);
        
        $statement = $this->entityManager->getConnection()->prepare($query);
        $statement->bindValue("nome", $nome);
        $statement->bindValue("data", $ymd);
        $result = $statement->execute();        
        $registros = $result->fetchAll();
        
        return $registros;

    }


    public function getPlayerHorasDetalhado($nome, $data){

        $query = "select p.nome, p.data_online, p.hora_online , p.hora_offline , 
        if(TIMEDIFF(p.hora_offline, p.hora_online) < 0, SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF(STR_TO_DATE('23:59:59', '%k:%i:%S'), p.hora_online)) + TIME_TO_SEC(TIMEDIFF(p.hora_offline, STR_TO_DATE('00:00:01', '%k:%i:%S')))), 
        TIMEDIFF(p.hora_offline, p.hora_online)) as 'total' 
        from players_online_horas p
        where p.nome = :nome and p.data_online = :data ";

        $ymd = \DateTime::createFromFormat('d/m/Y', $data);
        $ymd = ($ymd)->format('Y-m-d');
        
        $statement = $this->entityManager->getConnection()->prepare($query);
        $statement->bindValue("nome", $nome);
        $statement->bindValue("data", $ymd);
        $result = $statement->execute();        
        $registros = $result->fetchAll();
        
        return $registros;

    }


    public function getPlayerHorasPeriodo($nome, $dataI, $dataF){

        $query = "select p.nome, 
        sec_to_time(sum(time_to_sec(if(TIMEDIFF(p.hora_offline, p.hora_online) < 0, SEC_TO_TIME(TIME_TO_SEC(TIMEDIFF(STR_TO_DATE('23:59:59', '%k:%i:%S'), p.hora_online)) + TIME_TO_SEC(TIMEDIFF(p.hora_offline, STR_TO_DATE('00:00:01', '%k:%i:%S')))), 
        TIMEDIFF(p.hora_offline, p.hora_online))))) 
        as 'total' 
        from players_online_horas p
        where p.nome = :nome and p.data_online BETWEEN :data1 and :data2
        group by nome ";

        $ymd1 = \DateTime::createFromFormat('d/m/Y', $dataI);
        $ymd1 = ($ymd1)->format('Y-m-d');

        $ymd2 = \DateTime::createFromFormat('d/m/Y', $dataF);
        $ymd2 = ($ymd2)->format('Y-m-d');        
        
        $statement = $this->entityManager->getConnection()->prepare($query);
        $statement->bindValue("nome", $nome);
        $statement->bindValue("data1", $ymd1);
        $statement->bindValue("data2", $ymd2);
        $result = $statement->execute();        
        $registros = $result->fetchAll();
        
        return $registros;

    }     
    
    public function getAccCriada(){

        $query = "select p2.nome, p2.data_acc_criada from players p2 where p2.data_acc_criada in (
            select p.data_acc_criada 
            from players p 
            where p.data_acc_criada is not null
            group by p.data_acc_criada 
            having count(p.nome) > 1
            ) order by p2.data_acc_criada ";
        
        $statement = $this->entityManager->getConnection()->prepare($query);
        $result = $statement->execute();        
        $registros = $result->fetchAll();
        
        return $registros;

    }         

    public function getExperienciaPlayer($nome, $dataI, $dataF){

        $ymd1 = \DateTime::createFromFormat('d/m/Y', $dataI);
        $ymd1 = ($ymd1)->format('Y-m-d');

        $ymd2 = \DateTime::createFromFormat('d/m/Y', $dataF);
        $ymd2 = ($ymd2)->format('Y-m-d');            

        $query = "SELECT p1.nome, 
                            p1.experiencia as exp1,
                            COALESCE((SELECT p2.experiencia FROM players_highscore AS p2  WHERE p2.nome = p1.nome and p2.data = :data1),0) as exp2,
                            p1.level as lvl2,
                            (SELECT p2.level FROM players_highscore AS p2  WHERE p2.nome = p1.nome and p2.data = :data1) as lvl1,
                            COALESCE(p1.experiencia - COALESCE((SELECT p2.experiencia FROM players_highscore AS p2  WHERE p2.nome = p1.nome and p2.data = :data1),0), p1.experiencia) AS experiencia
                         FROM players_highscore AS p1 where p1.data = :data2 and p1.nome = :nome
                         ";
        
        $statement = $this->entityManager->getConnection()->prepare($query);
        $statement->bindValue("nome", $nome);
        $statement->bindValue("data1", $ymd1);
        $statement->bindValue("data2", $ymd2);
        $result = $statement->execute();        
        
        $registros = $result->fetchAll();
        
        return $registros;
    }

    public function getExperienciaPlayerTop10($dataI, $dataF){

        $ymd1 = $dataI;
        $ymd2 = $dataF;

        $query = "SELECT 
                    p1.nome, 
                    p1.experiencia as exp1,
                    COALESCE((SELECT p2.experiencia FROM players_highscore AS p2  WHERE p2.nome = p1.nome and p2.data = :data1),0) as exp2,
                    p1.level as lvl2,
                    (SELECT p2.level FROM players_highscore AS p2  WHERE p2.nome = p1.nome and p2.data = :data1) as lvl1,
                    COALESCE(p1.experiencia - COALESCE((SELECT p2.experiencia FROM players_highscore AS p2  WHERE p2.nome = p1.nome and p2.data = :data1),0), p1.experiencia) AS experiencia
                FROM players_highscore AS p1 where p1.data = :data2
                order by experiencia desc
                limit 20";
        
        $statement = $this->entityManager->getConnection()->prepare($query);
        $statement->bindValue("data1", $ymd1);
        $statement->bindValue("data2", $ymd2);
        $result = $statement->execute();        
        
        $registros = $result->fetchAll();
        
        return $registros;

    }    

    // /**
    //  * @return PlayersOnlineHoras[] Returns an array of PlayersOnlineHoras objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlayersOnlineHoras
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
