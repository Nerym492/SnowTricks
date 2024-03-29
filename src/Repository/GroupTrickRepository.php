<?php

namespace App\Repository;

use App\Entity\GroupTrick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupTrick>
 *
 * @method GroupTrick|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupTrick|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupTrick[]    findAll()
 * @method GroupTrick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupTrickRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupTrick::class);
    }

    /**
     * @param GroupTrick $entity
     * @param bool $flush
     * @return void
     */
    public function save(GroupTrick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param GroupTrick $entity
     * @param bool $flush
     * @return void
     */
    public function remove(GroupTrick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return GroupeTrick[] Returns an array of GroupeTrick objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GroupeTrick
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
