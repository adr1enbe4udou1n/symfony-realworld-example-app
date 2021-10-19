<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public const MAX_ITEMS_PER_PAGE = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function list(int $limit = 20, int $offset = 0)
    {
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder
            ->leftJoin('a.author', 'u')
            ->orderBy('a.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults(min(self::MAX_ITEMS_PER_PAGE, $limit));

        return new Paginator($queryBuilder);
    }
}
