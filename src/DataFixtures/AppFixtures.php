<?php

namespace App\DataFixtures;

use App\Entity\Mobile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for($i=1; $i <20;$i++) {
            $mobile = new Mobile();
            $mobile
                ->setBrand($faker->company)
                ->setModelName($faker->word)
                ->setOperatingSystem($faker->word)
                ->setCellularTechnology($faker->word)
                ->setMemoryStorage($faker->numberBetween(32, 256))
                ->setConnectivityTechnoloy($faker->word)
                ->setScreenSize($faker->randomFloat(2, 4, 7))
                ->setWirelessNetworkTechnology($faker->word)
                ->setReleaseDate($faker->dateTimeThisDecade)
                ->setBatteryAutonomy($faker->numberBetween(12, 48))
                ->setRamSize($faker->numberBetween(4, 16));

            $manager->persist($mobile);

        }

        $manager->persist($mobile);

        $manager->flush();
    }
}
