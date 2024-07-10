<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
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
        // Create an admin user
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword(
            $admin,
            'adminpassword'
        ));
        $admin->setActive(true);
        $manager->persist($admin);

        // Create a regular user
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'userpassword'
        ));
        $user->setActive(true);
        $manager->persist($user);

        // Create a first technician user
        $technicien1 = new User();
        $technicien1->setEmail('technicien1@example.com');
        $technicien1->setRoles(['ROLE_TECHNICIEN']);
        $technicien1->setPassword($this->passwordHasher->hashPassword(
            $technicien1,
            'technicienpassword1'
        ));
        $technicien1->setActive(true);
        $manager->persist($technicien1);

        // Create a second technician user
        $technicien2 = new User();
        $technicien2->setEmail('technicien2@example.com');
        $technicien2->setRoles(['ROLE_TECHNICIEN']);
        $technicien2->setPassword($this->passwordHasher->hashPassword(
            $technicien2,
            'technicienpassword2'
        ));
        $technicien2->setActive(true);
        $manager->persist($technicien2);

        // Create some tickets
        $ticket1 = new Ticket();
        $ticket1->setDateStart(new \DateTime());
        $ticket1->setDateEnd(new \DateTime('+1 hour'));
        $ticket1->setUser($user);
        $ticket1->setTechnicien($technicien1);
        $ticket1->setStatus('open');
        $ticket1->setDescription('Ticket 1 description');
        $manager->persist($ticket1);

        $ticket2 = new Ticket();
        $ticket2->setDateStart(new \DateTime());
        $ticket2->setDateEnd(new \DateTime('+2 hours'));
        $ticket2->setUser($user);
        $ticket2->setTechnicien($technicien2);
        $ticket2->setStatus('open');
        $ticket2->setDescription('Ticket 2 description');
        $manager->persist($ticket2);

        // Flush all to the database
        $manager->flush();
    }
}




