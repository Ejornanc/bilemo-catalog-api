<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\User;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // --- Création d’un client (compte pour se connecter à l’API)
        $client = new Client();
        $client->setName('BileMo Partner 1');
        $client->setEmail('partner@example.com');
        $client->setPassword($this->passwordHasher->hashPassword($client, 'password123'));
        $manager->persist($client);

        // --- Création de 10 utilisateurs liés à ce client
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->safeEmail());
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setClient($client);
            $manager->persist($user);
        }

        // --- Création de 5 produits
        for ($i = 0; $i < 5; $i++) {
            $product = new Product();
            $product->setName($faker->word() . ' Phone');
            $product->setDescription($faker->sentence(10));
            $product->setPrice($faker->randomFloat(2, 300, 1500));
            $product->setBrand($faker->company());
            $manager->persist($product);
        }

        $manager->flush();
    }
}
