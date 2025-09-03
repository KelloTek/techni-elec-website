<?php

namespace App\Repository;

use App\Entity\Document;
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

    public function search(int $user, string $type, string $search, string $date, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('u');

        if (!empty($search)) {
            $escapedSearch = '%' . addcslashes($search, '%_') . '%';

            switch ($type) {
                case 'id':
                    $qb->where("CONCAT(u.id, '') LIKE :search");
                    break;
                case 'name':
                    $qb->where('u.name LIKE :search');
                    break;
                case 'category':
                    $qb->leftJoin('u.category', 'c')
                        ->where('c.label LIKE :search');
                    break;
                case 'file':
                    $qb->leftJoin('u.file', 'f')
                        ->where('f.name LIKE :search');
                    break;
                default:
                    $qb->leftJoin('u.category', 'c')
                        ->leftJoin('u.file', 'f')
                        ->where("CONCAT(u.id, '') LIKE :search")
                        ->orWhere('u.name LIKE :search')
                        ->orWhere('c.label LIKE :search')
                        ->orWhere('f.name LIKE :search');
                    break;
            }

            $qb->setParameter('search', $escapedSearch);
        }

        if ($date) {
            $qb->andWhere('u.createdAt >= :startDate AND u.createdAt <= :endDate')
                ->setParameter('startDate', new DateTime($date))
                ->setParameter('endDate', (new DateTime($date))->modify('+1 day'));
        }

        $qb->orderBy('u.id', 'ASC')
            ->andWhere('u.user = :user')
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
