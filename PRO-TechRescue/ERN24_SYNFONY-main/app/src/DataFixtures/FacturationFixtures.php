<?php

namespace App\DataFixtures;

use App\Entity\Facturation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FacturationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Check if there are any users in the database
        $users = $manager->getRepository(User::class)->findAll();

        // If no users exist, create dummy users
        if (empty($users)) {
            for ($j = 0; $j < 5; $j++) {
                $user = new User();
                $user->setEmail($faker->unique()->email); // Set email
                $user->setPassword($faker->password); // Set password (or a proper hash)
                $user->setActive($faker->boolean); // Set active status

                $manager->persist($user);
                $users[] = $user; // Add the created user to the list
            }
            $manager->flush();
        }

        // Create Facturation fixtures
        for ($i = 0; $i < 10; $i++) {
            $facturation = new Facturation();

            $facturation->setInvoiceNumber($faker->unique()->numerify('INV####'));
            $facturation->setInvoiceDate($faker->dateTimeThisYear());
            $facturation->setDueDate($faker->dateTimeBetween('now', '+1 month'));
            $facturation->setTotalAmount($faker->randomFloat(2, 10, 500));
            $facturation->setTaxAmount($faker->randomFloat(2, 1, 50));
            $facturation->setValue($faker->text());
            $facturation->setDescription($faker->optional()->text());
            $facturation->setCreatedAt($faker->dateTimeBetween('-1 year', 'now'));
            $facturation->setUpdatedAt($faker->dateTimeBetween($facturation->getCreatedAt(), 'now'));

            // Assign a random user to the facturation
            $client = $faker->randomElement($users);
            $facturation->setClient($client);

            $manager->persist($facturation);
        }

        $manager->flush();
    }
}


