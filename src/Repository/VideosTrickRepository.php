<?php

namespace App\Repository;

use App\Entity\VideosTrick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VideosTrick>
 *
 * @method VideosTrick|null find($id, $lockMode = null, $lockVersion = null)
 * @method VideosTrick|null findOneBy(array $criteria, array $orderBy = null)
 * @method VideosTrick[]    findAll()
 * @method VideosTrick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VideosTrickRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideosTrick::class);
    }

    /**
     * @param VideosTrick $entity
     * @param bool $flush
     * @return void
     */
    public function save(VideosTrick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param VideosTrick $entity
     * @param bool $flush
     * @return void
     */
    public function remove(VideosTrick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return VideosTrick[] Returns an array of VideosTrick objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?VideosTrick
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
