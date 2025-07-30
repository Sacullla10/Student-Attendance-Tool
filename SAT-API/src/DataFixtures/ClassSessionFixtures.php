<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\ClassSession;

class ClassSessionFixtures
{
    public function load(ObjectManager $manager): void
    {
        foreach (range(1, 10) as $i) {
            $classSession = new ClassSession();
            $classSession->setSessionDate((new \DateTime())->modify("-" . ($i - 1) . " days"));
            $manager->persist($classSession);
        }

        $manager->flush();
    }
}
