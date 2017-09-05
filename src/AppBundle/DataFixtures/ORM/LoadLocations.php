<?php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Location;

class LoadLocations implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $names = array(
            'Extérieur couvert',
            'Extérieur non couvert',
            'Intérieur'
        );

        foreach ($names as $name) {
            $location = new Location();
            $location->setName($name);
            $manager->persist($location);
        }

        $manager->flush();
    }
}