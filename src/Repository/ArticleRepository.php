<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getCountOfArticlesCreated(User $user):int
    {
        return $this->createQueryBuilder('ac')
                    ->select('COUNT(ac)')
                    ->where('ac.author = :author')
                    ->setParameter('author', $user)
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getCountOfArticlesPublished(User $user):int
    {
        return $this->createQueryBuilder('ap')
            ->select('COUNT(ap)')
            ->where('ap.author = :author')
            ->andWhere('ap.publishedAt = true')
            ->setParameter('author', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
