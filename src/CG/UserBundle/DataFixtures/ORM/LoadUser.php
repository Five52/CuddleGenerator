<?php

namespace CG\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use CG\UserBundle\Entity\User;

class LoadUser implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = [
            ['user', 'user@user.com', 'userpass', ['ROLE_USER']],
            // ['evrard', 'evrard.caron@etu.unicaen.fr', 'evrardpass', ['ROLE_USER']],
            ['modo', 'modo@user.com', 'modopass', ['ROLE_MODERATOR']],
            ['admin', 'admin@cuddle-generator.com', 'adminpass', ['ROLE_ADMIN']],
        ];
        foreach ($data as $values) {
            $userManager = $this->container->get('fos_user.user_manager');
            $user = $userManager->createUser();
            $user->setUsername($values[0]);
            $user->setEmail($values[1]);
            $user->setPlainPassword($values[2]);
            $user->setRoles($values[3]);
            $user->setEnabled(true);
            $manager->persist($user);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}