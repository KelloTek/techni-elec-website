<?php

namespace App\Repository;

use App\Entity\Request;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Request>
 */
class RequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Request::class);
    }

    public function search(string $type, string $search, string $date, int $limit, int $offset, User $user = null): array
    {
        $qb = $this->createQueryBuilder('q');

        if ($user !== null) {
            $qb->andWhere('q.user = :user')
                ->setParameter('user', $user);
        }

        if (!empty($search)) {
            $escapedSearch = '%' . addcslashes($search, '%_') . '%';

            switch ($type) {
                case 'id':
                    $qb->where("CONCAT(q.id, '') LIKE :search");
                    break;
                case 'title':
                    $qb->where('LOWER(q.title) LIKE :search');
                    break;
                default:
                    $qb->where("CONCAT(q.id, '') LIKE :search")
                        ->orWhere('LOWER(q.title) LIKE :search');
                    break;
            }

            $qb->setParameter('search', strtolower($escapedSearch));
        }

        if ($date) {
            $qb->andWhere('q.updatedAt >= :startDate AND q.updatedAt <= :endDate')
                ->setParameter('startDate', new DateTime($date))
                ->setParameter('endDate', (new DateTime($date))->modify('+1 day'));
        }

        $qb->orderBy('q.updatedAt', 'DESC')
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
    //     * @return Request[] Returns an array of Request objects
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

    //    public function findOneBySomeField($value): ?Request
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
