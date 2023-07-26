<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(
        ManagerRegistry $registry,
        private ParameterBagInterface $parameterBag
    ) {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @param Comment $entity
     * @param bool $flush
     * @return void
     */
    public function save(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Comment $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Retrieves all comments or a limited number if commentsReloaded parameter is set
     *
     * @param array $orderBy          ORDER BY in the query. Example : ['fieldToOrder' => 'DESC']
     * @param int   $commentsReloaded Comments already loaded in the page before the query
     * @return array
     */
    public function findAllOrdered(array $orderBy, int $commentsReloaded = 0): array
    {
        $commentListLimit = $this->parameterBag->get('comments_list_limit');
        // Number of comments loaded by default
        $nbCommentsToLoad = $commentListLimit;
        $remainingComments = 0;

        $commentsCountQuery = $this->createQueryBuilder('c');
        $commentsCountQuery->select('COUNT(c) AS nbComments');
        $commentsCountResult = $commentsCountQuery->getQuery()->getResult();
        $commentsCount = $commentsCountResult[0]['nbComments'];

        if ($commentsReloaded > 0) {
            $remainingComments = $commentsCount - $commentsReloaded;
        }

        if ($remainingComments > 0 && $remainingComments >= $commentListLimit) {
            // There will still be comments to load with the load more button
            $nbCommentsToLoad = $commentsReloaded + $commentListLimit;
        } elseif ($remainingComments > 0) {
            // All comments will be loaded
            $nbCommentsToLoad = $commentsCount;
        }

        $commentQuery = $this->createQueryBuilder('c');
        $commentQuery->select('c AS data', 'u.pseudo AS userPseudo');
        $commentQuery->leftJoin('c.user', 'u');

        foreach ($orderBy as $fieldName => $direction) {
            $commentQuery->addOrderBy('c.'.$fieldName, $direction);
        }

        return $commentQuery->setMaxResults($nbCommentsToLoad)->getQuery()->getResult();
    }

//    /**
//     * @return Commentaire[] Returns an array of Commentaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Commentaire
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
