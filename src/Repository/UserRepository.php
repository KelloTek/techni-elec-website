<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function getCount(): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function search(string $search, string $date, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('u');

        if (!empty($search)) {
            $escapedSearch = '%' . addcslashes($search, '%_') . '%';
            $qb->leftJoin('u.address', 'a')
                ->where("CONCAT(u.id, '') LIKE :search")
                ->orWhere('u.name LIKE :search')
                ->orWhere('u.email LIKE :search')
                ->orWhere('u.phone LIKE :search')
                ->orWhere('a.line LIKE :search')
                ->orWhere("CONCAT(a.zipCode, '') LIKE :search")
                ->orWhere('a.city LIKE :search')
                ->setParameter('search', $escapedSearch);
        }

        if ($date) {
            $qb->andWhere('u.createdAt >= :startDate AND u.createdAt <= :endDate')
                ->setParameter('startDate', new DateTime($date))
                ->setParameter('endDate', (new DateTime($date))->modify('+1 day'));
        }

        return $qb->orderBy('u.id', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function getSearchCount(string $search, string $date): int
    {
        $qb = $this->createQueryBuilder('u');

        if (!empty($search)) {
            $escapedSearch = '%' . addcslashes($search, '%_') . '%';
            $qb->leftJoin('u.address', 'a')
                ->where("CONCAT(u.id, '') LIKE :search")
                ->orWhere('u.name LIKE :search')
                ->orWhere('u.email LIKE :search')
                ->orWhere('u.phone LIKE :search')
                ->orWhere('a.line LIKE :search')
                ->orWhere("CONCAT(a.zipCode, '') LIKE :search")
                ->orWhere('a.city LIKE :search')
                ->setParameter('search', $escapedSearch);
        }

        if ($date) {
            $qb->andWhere('u.createdAt >= :startDate AND u.createdAt <= :endDate')
                ->setParameter('startDate', new DateTime($date))
                ->setParameter('endDate', (new DateTime($date))->modify('+1 day'));
        }

        return $qb->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
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
