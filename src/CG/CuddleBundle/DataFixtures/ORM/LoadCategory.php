<?php

namespace CG\CuddleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use CG\CuddleBundle\Entity\Category;

class LoadCategory implements FixtureInterface, OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = [
            'Humour',
            'Cuisine',
            'Charisme',
            'Intelligence'
        ];
        foreach ($data as $name) {
            $category = new Category;
            $category->setName($name);
            $manager->persist($category);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}