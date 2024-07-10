<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

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

        // Create a technicien user
        $technicien = new User();
        $technicien->setEmail('technicien@example.com');
        $technicien->setRoles(['ROLE_TECHNICIEN']);
        $technicien->setPassword($this->passwordHasher->hashPassword(
            $technicien,
            'technicienpassword'
        ));
        $technicien->setActive(true);
        $manager->persist($technicien);

        // Create some tickets
        $ticket1 = new Ticket();
        $ticket1->setDateStart(new \DateTime());
        $ticket1->setDateEnd(new \DateTime('+1 hour'));
        $ticket1->setUser($user);
        $ticket1->setTechnicien($technicien);
        $manager->persist($ticket1);

        $ticket2 = new Ticket();
        $ticket2->setDateStart(new \DateTime());
        $ticket2->setDateEnd(new \DateTime('+2 hours'));
        $ticket2->setUser($user);
        $ticket2->setTechnicien($technicien);
        $manager->persist($ticket2);

        // Flush all to the database
        $manager->flush();
    }
}




