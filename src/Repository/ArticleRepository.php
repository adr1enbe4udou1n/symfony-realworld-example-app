<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
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

    public function getQuery()
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.author', 'u')->addSelect('u')
            ->leftJoin('a.favoritedBy', 'fu')->addSelect('fu')
            ->leftJoin('a.tags', 't')->addSelect('t');
    }

    public function list(int $limit = 20, int $offset = 0, $author = null, $tag = null, $favorited = null)
    {
        $queryBuilder = $this->getQuery();

        if ($author) {
            $queryBuilder
                ->where('LOWER(u.name) LIKE :author')
                ->setParameter('author', "%{$author}%");
        }

        if ($tag) {
            $queryBuilder
                ->where('LOWER(t.name) LIKE :tag')
                ->setParameter('tag', "%{$tag}%");
        }

        if ($favorited) {
            $queryBuilder
                ->where('LOWER(fu.name) LIKE :user')
                ->setParameter('user', "%{$favorited}%");
        }

        return new Paginator($queryBuilder
            ->orderBy('a.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults(min(self::MAX_ITEMS_PER_PAGE, $limit)));
    }

    public function feed(User $user, int $limit = 20, int $offset = 0)
    {
        $queryBuilder = $this->getQuery()
            ->where(':user MEMBER OF u.followers')
            ->setParameter('user', $user);

        return new Paginator($queryBuilder
            ->orderBy('a.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults(min(self::MAX_ITEMS_PER_PAGE, $limit)));
    }
}
