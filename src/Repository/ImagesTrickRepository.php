<?php

namespace App\Repository;

use App\Entity\ImagesTrick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImagesTrick>
 *
 * @method ImagesTrick|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImagesTrick|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImagesTrick[]    findAll()
 * @method ImagesTrick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImagesTrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImagesTrick::class);
    }

    public function save(ImagesTrick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ImagesTrick $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ImagesTrick[] Returns an array of ImagesTrick objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findOneByTrick($trickId): ?ImagesTrick
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.trick = :val')
            ->setParameter('val', $trickId)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAllExceptFirst($trickId): array
    {
        $nbImagesQuery = $this->createQueryBuilder('i')
            ->select('COUNT(i) AS nbImages')
            ->getQuery()
            ->getResult();

        return $this->createQueryBuilder('i')
            ->andWhere('i.trick = :val')
            ->setParameter('val', $trickId)
            ->orderBy('i.id', 'ASC')
            ->setFirstResult(1)
            ->setMaxResults($nbImagesQuery[0]['nbImages'])
            ->getQuery()
            ->getResult();
    }
}
