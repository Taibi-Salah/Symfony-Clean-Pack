<?php

namespace App\DataFixtures;

use App\Entity\ContactInformation;
use App\Entity\Intervention;
use App\Entity\Stock;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadContactInformations($manager);
        $this->loadStocks($manager);
        $this->loadInterventions($manager);
        $this->loadTickets($manager);
        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        // Create an admin user
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword(
            $admin,
            'admin'
        ));
        $admin->setActive(true);
        $manager->persist($admin);
        $this->addReference('user-0', $admin);

        // Create a regular user
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'user'
        ));
        $user->setActive(true);
        $manager->persist($user);
        $this->addReference('user-1', $user);

        // Create a first technician user
        $technicien1 = new User();
        $technicien1->setEmail('technicien1@example.com');
        $technicien1->setRoles(['ROLE_TECHNICIEN']);
        $technicien1->setPassword($this->passwordHasher->hashPassword(
            $technicien1,
            'technicien1'
        ));
        $technicien1->setActive(true);
        $manager->persist($technicien1);
        $this->addReference('user-2', $technicien1);

        // Create a second technician user
        $technicien2 = new User();
        $technicien2->setEmail('technicien2@example.com');
        $technicien2->setRoles(['ROLE_TECHNICIEN']);
        $technicien2->setPassword($this->passwordHasher->hashPassword(
            $technicien2,
            'technicien2'
        ));
        $technicien2->setActive(true);
        $manager->persist($technicien2);
        $this->addReference('user-3', $technicien2);
    }

    public function loadContactInformations(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $contact = new ContactInformation();
            $contact->setLastName($faker->lastName);
            $contact->setFirstName($faker->firstName);
            $contact->setPhoneNumber($faker->phoneNumber);
            $manager->persist($contact);
            $this->addReference('contact-' . $i, $contact);
        }
    }

    public function loadStocks(ObjectManager $manager)
    {
        $stocks = [
            'ordinateur', 'clavier', 'souris',
            'écran', 'câble', 'imprimante',
            'scanner', 'routeur', 'modem',
            'disque dur', 'SSD', 'barrette de RAM',
            'carte mère', 'carte graphique',
            'processeur', 'ventilateur', 'boîtier',
            'alimentation', 'câble HDMI', 'câble USB'
        ];

        foreach ($stocks as $s => $stock) {
            $st = new Stock();
            $st->setLabel($stock);
            $st->setReferenceNb('REF-' . strtoupper($stock));
            $st->setQuantity(rand(1, 100));
            $st->setActive(true);
            $manager->persist($st);
            $this->addReference('stock-' . $s, $st);
        }
    }

    public function loadInterventions(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $intervention = new Intervention();
            $labelArray = $faker->words(3);

            // Ensure that $labelArray is an array
            if (!is_array($labelArray)) {
                $labelArray = [$labelArray]; // Convert to array if it's not
            }
            $label = join(' ', $labelArray); // Join words with space

            $intervention->setLabel($label);
            $manager->persist($intervention);
            $this->addReference('intervention-' . $i, $intervention);
        }
    }



    public function loadTickets(ObjectManager $manager)
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
    }
}
