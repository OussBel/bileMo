<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Mobile;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * @param ObjectManager $manager
     * @return void
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i < 20; $i++) {
            $mobile = new Mobile();

            $mobile->setBrand($faker->company)
                ->setModelName($faker->word)
                ->setMemoryStorage($faker->numberBetween(32, 256))
                ->setScreenSize($faker->randomFloat(2, 4, 7))
                ->setDescription($faker->paragraph)
                ->setWirelessNetwork('Wifi');


            $manager->persist($mobile);
        }

        $client = new Client();
        $client->setFirstName('client1')
            ->setLastName('lastName')
            ->setRoles(['ROLE_USER'])
            ->setEmail('client@bilemo.com')
            ->setSiret(891456787)
            ->setAddress('20 avenue Jean Jaures 75016 Paris France')
            ->setPassword($this->passwordHasher->hashPassword($client, 'password'))
            ->setPhone(111111111);

        $manager->persist($client);

        for ($i = 1; $i < 20; $i++) {
            $user = new User();
            $user->setLastName($faker->lastName)
                ->setFirstName($faker->lastName)
                ->setClient($client);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
