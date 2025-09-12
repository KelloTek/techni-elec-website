<?php

namespace App\Repository;

use App\Entity\RequestCategory;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RequestCategory>
 */
class RequestCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RequestCategory::class);
    }

    public function search(string $type, string $search, string $date, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('q');

        if (!empty($search)) {
            $escapedSearch = '%' . addcslashes($search, '%_') . '%';

            switch ($type) {
                case 'id':
                    $qb->where("CONCAT(q.id, '') LIKE :search");
                    break;
                case 'label':
                    $qb->where('LOWER(q.label) LIKE :search');
                    break;
                default:
                    $qb->where("CONCAT(q.id, '') LIKE :search")
                        ->orWhere('LOWER(q.label) LIKE :search');
                    break;
            }

            $qb->setParameter('search', strtolower($escapedSearch));
        }

        if ($date) {
            $qb->andWhere('q.createdAt >= :startDate AND q.createdAt <= :endDate')
                ->setParameter('startDate', new DateTime($date))
                ->setParameter('endDate', (new DateTime($date))->modify('+1 day'));
        }

        $qb->orderBy('q.id', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();

        $paginator = new Paginator($qb, true);

        return [
            'results' => iterator_to_array($paginator),
            'count' => count($paginator),
        ];
    }

    //    /**
    //     * @return RequestCategory[] Returns an array of RequestCategory objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?RequestCategory
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
