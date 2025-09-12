<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Document>
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function search(string $type, string $search, string $date, int $limit, int $offset, User $user = null): array
    {
        $qb = $this->createQueryBuilder('q');

        if ($user !== null) {
            $qb->andWhere('q.user = :user')
                ->setParameter('user', $user->getId());
        }

        if (!empty($search)) {
            $escapedSearch = '%' . addcslashes($search, '%_') . '%';

            switch ($type) {
                case 'id':
                    $qb->where("CONCAT(q.id, '') LIKE :search");
                    break;
                case 'name':
                    $qb->where('LOWER(q.name) LIKE :search');
                    break;
                case 'user':
                    $qb->leftJoin('q.user', 'u')
                        ->where("LOWER(u.name) LIKE :search");
                    break;
                case 'category':
                    $qb->leftJoin('q.category', 'c')
                        ->where('LOWER(c.label) LIKE :search');
                    break;
                case 'file':
                    $qb->leftJoin('q.file', 'f')
                        ->where('LOWER(f.name) LIKE :search');
                    break;
                default:
                    $qb->leftJoin('q.category', 'c')
                        ->leftJoin('q.file', 'f')
                        ->where("CONCAT(q.id, '') LIKE :search")
                        ->orWhere('LOWER(q.name) LIKE :search')
                        ->orWhere('LOWER(c.label) LIKE :search')
                        ->orWhere('LOWER(f.name) LIKE :search');
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

    public function searchByOneUser(int $user, string $type, string $search, string $date, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('q');

        if (!empty($search)) {
            $escapedSearch = '%' . addcslashes($search, '%_') . '%';

            switch ($type) {
                case 'id':
                    $qb->where("CONCAT(q.id, '') LIKE :search");
                    break;
                case 'name':
                    $qb->where('LOWER(q.name) LIKE :search');
                    break;
                case 'category':
                    $qb->leftJoin('q.category', 'c')
                        ->where('LOWER(c.label) LIKE :search');
                    break;
                case 'file':
                    $qb->leftJoin('q.file', 'f')
                        ->where('LOWER(f.name) LIKE :search');
                    break;
                default:
                    $qb->leftJoin('q.category', 'c')
                        ->leftJoin('q.file', 'f')
                        ->where("CONCAT(q.id, '') LIKE :search")
                        ->orWhere('LOWER(q.name) LIKE :search')
                        ->orWhere('LOWER(c.label) LIKE :search')
                        ->orWhere('LOWER(f.name) LIKE :search');
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
            ->andWhere('q.user = :user')
            ->setParameter('user', $user)
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
    //     * @return Document[] Returns an array of Document objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Document
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
