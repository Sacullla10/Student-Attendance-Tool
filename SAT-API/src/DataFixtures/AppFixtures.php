<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
         // 1. Executa StudentFixtures
        $studentFixtures = new StudentFixtures();
        $studentFixtures->load($manager);

        // 2. Executa ClassSessionFixtures
        $classSessionFixtures = new ClassSessionFixtures();
        $classSessionFixtures->load($manager);

        // 3. Executa AttendaceFixtures
        $attendaceFixtures = new AttendanceFixtures();
        $attendaceFixtures->load($manager);

        $manager->flush();
    }
}
