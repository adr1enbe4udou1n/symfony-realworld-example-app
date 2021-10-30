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

    public function createQuery($alias)
    {
        return $this->createQueryBuilder($alias)
            ->leftJoin("$alias.author", 'u')
            ->leftJoin("$alias.favoritedBy", 'fu')
            ->leftJoin("$alias.tags", 't')
            ->addSelect('u', 'fu', 't');
    }

    public function list(int $limit = 20, int $offset = 0, $author = null, $tag = null, $favorited = null)
    {
        $subQuery = $this->createQueryBuilder('a2')
            ->select('a2.id')
            ->leftJoin('a2.author', 'u2')
            ->leftJoin('a2.tags', 't2')
            ->leftJoin('a2.favoritedBy', 'fu2');

        $query = $this->createQuery('a');

        if ($author) {
            $subQuery->where('LOWER(u2.name) LIKE :author');
            $query->setParameter('author', "%{$author}%");
        }

        if ($tag) {
            $subQuery->where('LOWER(t2.name) LIKE :tag');
            $query->setParameter('tag', "%{$tag}%");
        }

        if ($favorited) {
            $subQuery->where('LOWER(fu2.name) LIKE :user');
            $query->setParameter('user', "%{$favorited}%");
        }

        return new Paginator($query
            ->where($this->getEntityManager()->getExpressionBuilder()->in(
                'a.id',
                $subQuery->getDQL()
            ))
            ->orderBy('a.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults(min(self::MAX_ITEMS_PER_PAGE, $limit)));
    }

    public function feed(User $user, int $limit = 20, int $offset = 0)
    {
        $queryBuilder = $this->createQuery('a')
            ->where(':user MEMBER OF u.followers')
            ->setParameter('user', $user);

        return new Paginator($queryBuilder
            ->orderBy('a.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults(min(self::MAX_ITEMS_PER_PAGE, $limit)));
    }
}
