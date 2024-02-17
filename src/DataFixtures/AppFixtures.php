<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Mobile;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i < 20; $i++) {
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

        $client = new Client();
        $client->setEmail('client@example.com');
        $client->setRoles(['ROLE_USER']);
        $client->setPassword($this->passwordHasher->hashPassword($client, 'password123'));
        $client->setFirstName('Emmanuel');
        $client->setLastName('Doen');
        $client->setAddress('123 rue Jean Jaures 75016 Paris');
        $client->setSlug('emmanuel-doen');
        $client->setSiret(123456789);
        $client->setPhone(33745267826);
        $client->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($client);

        for ($i = 1; $i < 20; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setRoles(['ROLE_USER']);
            $user->setEmail($faker->unique()->email);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $user->setClient($client);

            $manager->persist($user);
        }


        $manager->flush();
    }
}
