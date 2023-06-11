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
}
