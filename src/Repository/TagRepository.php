<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function list()
    {
        return $this->createQueryBuilder('t')
            ->select('t.name')
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getSingleColumnResult()
        ;
    }

    public function byNames(array $names)
    {
        return $this->createQueryBuilder('t')
            ->where('t.name IN (:names)')
            ->setParameter('names', $names)
            ->getQuery()
            ->getResult()
        ;
    }
}
