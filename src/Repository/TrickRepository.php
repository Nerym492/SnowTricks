<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

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
    private ParameterBagInterface $parameterBag;

    public function __construct(ManagerRegistry $registry, ParameterBagInterface $parameterBag)
    {
        parent::__construct($registry, Trick::class);
        $this->parameterBag = $parameterBag;
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

    public function findAllTricksBy(array $orderBy, int $tricksReloaded = 0): array
    {
        $tricksListLoadLimit = $this->parameterBag->get('tricks_list_load_limit');
        $nbTricksToLoad = $tricksListLoadLimit;
        $nbTricksToAdd = 0;

        if (0 < $tricksReloaded) {
            $tricksCountQuery = $this->createQueryBuilder('t');
            $tricksCountQuery->select('COUNT(t) AS nbTricks');
            $tricksCountResult = $tricksCountQuery->getQuery()->getResult();
            $tricksCount = $tricksCountResult[0]['nbTricks'];
            $nbTricksToAdd = ($tricksCount - $tricksReloaded);
        }

        if (0 < $nbTricksToAdd) {
            $nbTricksToLoad = $tricksReloaded + $tricksListLoadLimit;
        } elseif (0 === $nbTricksToAdd && 0 < $tricksReloaded) {
            $nbTricksToLoad = $tricksReloaded;
        }

        $tricksQuery = $this->createQueryBuilder('t');

        $tricksQuery->select('t AS data', 'g.name AS group_name', 'it.fileName')
            ->leftJoin('t.group_trick', 'g')
            ->leftJoin('t.imagesTricks', 'it')
            ->andWhere('it.id = (
                SELECT MIN(it2.id)
                FROM App\Entity\ImagesTrick it2
                WHERE it2.trick = t
            )')
            ->setMaxResults($nbTricksToLoad);

        foreach ($orderBy as $field => $direction) {
            $tricksQuery->addOrderBy('t.'.$field, $direction);
        }

        return $tricksQuery->getQuery()->getResult();
    }

    public function countTricks(): int
    {
        $tricksCount = $this->createQueryBuilder('t');
        $tricksCount->select('COUNT(t) AS nbTricks');

        return $tricksCount->getQuery()->getResult()[0]['nbTricks'];
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
