<?php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Gender;

class LoadGenders implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $names = array(
            'Homme',
            'Femme',
            'Enfant',
        );

        foreach ($names as $name) {
            $gender = new Gender();
            $gender->setName($name);
            $manager->persist($gender);
        }

        $manager->flush();
    }
}