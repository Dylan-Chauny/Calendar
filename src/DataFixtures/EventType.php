<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
;

class EventType extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $eventType = new \App\Entity\EventType();
        $eventType->setCode('off');
        $eventType->setColor('orange');
        $eventType->setLabel("Repos");
        $manager->persist($eventType);
        $manager->flush();

        $eventType = new \App\Entity\EventType();
        $eventType->setCode('holiday');
        $eventType->setColor('grey');
        $eventType->setLabel("Férié");
        $manager->persist($eventType);
        $manager->flush();

        $eventType = new \App\Entity\EventType();
        $eventType->setCode('work');
        $eventType->setColor('#7a5afb');
        $eventType->setLabel("Travail");
        $manager->persist($eventType);
        $manager->flush();
    }
}
