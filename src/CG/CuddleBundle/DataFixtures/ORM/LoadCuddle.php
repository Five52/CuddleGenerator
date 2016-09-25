<?php

namespace CG\CuddleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use CG\CuddleBundle\Entity\Cuddle;

class LoadCuddle implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = [
            ['ta cuisin et excélente', 'user', 'Cuisine', false],
            ['tes vraiment pas beau', 'user', 'Intelligence', false],
            ['Tu danses de mieux en mieux', 'user', 'Charisme', false],
            ["J'adore les gâteaux que tu fais", 'user', 'Cuisine', false],
            ['Passer une journée avec toi est un bonheur', 'user', 'Charisme', true],
            ["J'aime ta façon de rire", 'user', 'Charisme', true]
        ];
        foreach ($data as $values) {
            $cuddle = new Cuddle;
            $cuddle->setContent($values[0]);
            $userManager = $this->container->get('fos_user.user_manager');
            $user = $userManager->findUserBy(['username' => $values[1]]);
            $cuddle->setAuthor($user);
            $category = $manager->getRepository('CGCuddleBundle:Category')->findOneByName($values[2]);
            $cuddle->setCategory($category);
            $cuddle->setValidated($values[3]);
            $manager->persist($cuddle);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}