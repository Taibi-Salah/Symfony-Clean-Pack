<?php

namespace App\DataFixtures;

use App\Entity\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class StockFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
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

        $suppliers = ['user-4', 'user-5'];

        foreach ($stocks as $s => $stock) {
            $st = new Stock();
            $st->setLabel($stock);
            $st->setReferenceNb('REF-' . strtoupper($stock));
            $st->setQuantity(rand(1, 100));
            $st->setActive(true);

            try {
                $st->setSupplier($this->getReference($suppliers[rand(0, 1)]));
            } catch (\Exception $e) {
                echo "Error setting supplier for stock item $stock: " . $e->getMessage() . "\n";
                continue;
            }

            $manager->persist($st);
            $this->addReference('stock-' . $s, $st);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['StockFixtures'];
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
