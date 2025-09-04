<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use DateTime;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
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
                case 'name':
                    $qb->where('LOWER(q.name) LIKE :search');
                    break;
                case 'email':
                    $qb->where('LOWER(q.email) LIKE :search');
                    break;
                case 'phone':
                    $qb->where('LOWER(q.phone) LIKE :search');
                    break;
                case 'address':
                    $qb->leftJoin('q.address', 'a')
                        ->where('LOWER(a.line) LIKE :search')
                        ->orWhere("CONCAT(a.zipCode, '') LIKE :search")
                        ->orWhere('LOWER(a.city) LIKE :search');
                    break;
                default:
                    $qb->leftJoin('q.address', 'a')
                        ->where("CONCAT(q.id, '') LIKE :search")
                        ->orWhere('LOWER(q.name) LIKE :search')
                        ->orWhere('LOWER(q.email) LIKE :search')
                        ->orWhere('LOWER(q.phone) LIKE :search')
                        ->orWhere('LOWER(a.line) LIKE :search')
                        ->orWhere("CONCAT(a.zipCode, '') LIKE :search")
                        ->orWhere('LOWER(a.city) LIKE :search');
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
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
