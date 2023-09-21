<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
;

class EventType extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $arrayDatas = [
            ['off', 'orange', 'Repos'],
            ['holiday', 'grey', 'Férié'],
            ['work', '#7a5afb', 'Travail'],
        ];

        foreach ($arrayDatas as $arrayData) {
            $eventType = new \App\Entity\EventType();
            $eventType->setCode($arrayData[0]);
            $eventType->setColor($arrayData[1]);
            $eventType->setLabel($arrayData[2]);
            $manager->persist($eventType);
        }

        $manager->flush();
    }
}
