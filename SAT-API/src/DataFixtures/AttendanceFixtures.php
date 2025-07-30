<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Attendance;
use App\Entity\ClassSession;
use App\Entity\Student;

class AttendanceFixtures
{
    public function load(ObjectManager $manager): void
    {
        $StudentList = $manager->getRepository(Student::class)->findAll();
        $ClassSessionList = $manager->getRepository(ClassSession::class)->findAll();

        foreach ($ClassSessionList as $classSession) {
            foreach ($StudentList as $student) {
                $attendance = new Attendance();
                $attendance->setClassSession($classSession);
                $attendance->setStudent($student);
                $attendance->setIsPresent((bool) random_int(0, 1));
                $manager->persist($attendance);
            }
        }

        $manager->flush();
    }
}
