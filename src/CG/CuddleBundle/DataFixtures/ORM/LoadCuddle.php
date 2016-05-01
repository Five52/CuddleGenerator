<?php

namespace CG\CuddleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use CG\CuddleBundle\Entity\Cuddle;

class LoadCuddle implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = [
            ['Ta cuisine est excellente', 'Alex'],
            ['Tu danses de mieux en mieux', 'John'],
            ["J'aime ta façon de rire", 'Simon']
        ];
        foreach ($data as $values) {
            $cuddle = new Cuddle;
            $cuddle->setContent($values[0]);
            $cuddle->setAuthor($values[1]);
            $manager->persist($cuddle);
        }
        $manager->flush();
    }
}