<?php

namespace App\DataFixtures;

use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class TicketFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $ticket = new Ticket();

            $ticket->setDateStart($faker->dateTimeBetween('now', '+1 month'))
                ->setDateEnd($faker->dateTimeBetween('+1 month', '+2 month'))
                ->setUser($this->getReference('user-' . rand(0, 3)))
                ->setTechnicien($this->getReference('user-' . rand(0, 3)))
                ->setIntervention($this->getReference('intervention-' . rand(0, 9)))
                ->setStatus('open')
                ->setDescription($faker->sentence(10));

            $manager->persist($ticket);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            InterventionFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['TicketFixtures'];
    }
}
