<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private $passwordHasher;

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

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['UsersFixtures'];
    }
}
