<?php

namespace App\Repository;

use App\Entity\Players;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Players|null find($id, $lockMode = null, $lockVersion = null)
 * @method Players|null findOneBy(array $criteria, array $orderBy = null)
 * @method Players[]    findAll()
 * @method Players[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Players::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Players $entity, bool $flush = true): void
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
    public function remove(Players $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getPlayers()
    {
        $players = $this
            ->createQueryBuilder('p')
            ->select("p")
            //->where("cv.empresa = :empresa and cv.nStatusNfe = '100' and cv.compraOuVenda = 'V' and cv.dataEmissao BETWEEN :data AND :fim")
            //->andWhere('cv.ambiente = 1')
            //->andWhere('cv.finalidade != 4')
            //->setParameter('empresa', $empresa)
            //->setParameter('data', $data->format('Y-m-d') . ' ' . $hora->format('H:i:s'))
            //->setParameter('fim', $hoje->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult();

        return $players;
    }


    // /**
    //  * @return Players[] Returns an array of Players objects
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
    public function findOneBySomeField($value): ?Players
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
