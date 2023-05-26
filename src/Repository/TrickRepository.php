<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trick>
 *
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    public function save(Trick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Trick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllTricksBy(array $orderBy): array
    {
        $tricksQuery = $this->createQueryBuilder('t');
        $imageSubQuery = $this->createQueryBuilder('it2');

        $imageSubQuery->select('it2_sub.id')
            ->from('\App\Entity\ImagesTrick', 'it2_sub')
            ->where('it2_sub.trick = t')
            ->orderBy('it2_sub.id', 'DESC')
            ->setMaxResults(1);

        $tricksQuery->select(['t AS data', 'g.nom AS nom_groupe', 'it.nomFichier'])
                    ->leftJoin('t.groupe_trick', 'g')
                    ->leftJoin('t.imagesTricks', 'it')
                    ->andWhere($tricksQuery->expr()->in(
                        'it.id', $imageSubQuery->getDQL()
                    ));

        foreach ($orderBy as $field => $direction) {
            $tricksQuery->addOrderBy('t.'.$field, $direction);
        }

        return $tricksQuery->getQuery()->getResult();
    }

//    /**
//     * @return Trick[] Returns an array of Trick objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Trick
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
