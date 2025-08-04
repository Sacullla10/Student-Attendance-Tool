<?php

namespace App\Repository;

use App\Entity\Attendance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Attendance>
 */
class AttendanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attendance::class);
    }

    public function getStudentAttendanceSummary(int $studentId): array
    {
        return $this->createQueryBuilder('a')
            ->select('
                COUNT(a.id) AS totalClasses,
                SUM(CASE WHEN a.is_present = true THEN 1 ELSE 0 END) AS totalPresent,
                SUM(CASE WHEN a.is_present = false THEN 1 ELSE 0 END) AS totalAbsent'
            )
            ->andWhere('a.student = :studentId')
            ->setParameter('studentId', $studentId)
            ->getQuery()
            ->getOneOrNullResult() ?? [
                'totalClasses' => 0,
                'totalPresent' => 0,
                'totalAbsent' => 0,
            ];
    }

    // public function findAttendancesBySession(int $classSessionId): array
    // {
    //     return $this->createQueryBuilder('a')
    //         ->leftJoin('a.student', 's')
    //         ->addSelect('s')
    //         ->andWhere('a.class_session = :sessionId')
    //         ->setParameter('sessionId', $classSessionId)
    //         ->getQuery()
    //         ->getResult();
    // }

    //    /**
    //     * @return Attendance[] Returns an array of Attendance objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Attendance
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
