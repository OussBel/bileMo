<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Phone;
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

        //Ajout des téléphones
        for ($i = 1; $i < 20; $i++) {
            $phone = new Phone();

            $phone->setBrand($faker->company)
                ->setModelName($faker->word)
                ->setMemoryStorage($faker->numberBetween(32, 256))
                ->setScreenSize($faker->randomFloat(2, 4, 7))
                ->setDescription($faker->paragraph)
                ->setWirelessNetwork('Wifi');


            $manager->persist($phone);
        }

        //Ajout d'un client (Entreprise)
        $enterprise = new Client();
        $enterprise->setName($faker->company)
            ->setDescription($faker->paragraph);

        $manager->persist($enterprise);

        // Création d'un utilisateur qui a un role client qui lui permet d'ajouter ou supprimer un autre utilisateur
        $client = new User();
        $client->setLastName('Doe')
            ->setFirstName('John')
            ->setSiren(356879999)
            ->setMobile(187954123)
            ->setAddress($faker->address)
            ->setEmail('client@bilemo.fr')
            ->setPassword($this->passwordHasher->hashPassword($client, 'password'))
            ->setRoles(['ROLE_ADMIN'])
            ->setClient($enterprise);

        $manager->persist($client);

        // Ajout des utilisateurs
        for ($i = 1; $i < 20; $i++) {
            $user = new User();
            $user->setLastName($faker->lastName)
                ->setFirstName($faker->lastName)
                ->setSiren(356784224)
                ->setMobile(18791244)
                ->setAddress($faker->address)
                ->setEmail($faker->unique()->email())
                ->setPassword($this->passwordHasher->hashPassword($user, 'password'))
                ->setRoles(['ROLE_USER'])
                ->setClient($enterprise);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
