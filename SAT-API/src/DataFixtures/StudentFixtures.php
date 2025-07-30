<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Student;

class StudentFixtures
{
    public function load(ObjectManager $manager): void
    {
        
        foreach (range(1, 30) as $i) {
            $student = new Student();
            $student->setName('Student ' . $i);
            $manager->persist($student);
        }

        $manager->flush();
    }
}
