<?php

namespace App\DataFixtures;

use App\Entity\Stock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class StockFixtures extends Fixture implements FixtureGroupInterface
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

        foreach ($stocks as $s => $stock) {
            $st = new Stock();
            $st->setLabel($stock);
            $st->setReferenceNb('REF-' . strtoupper($stock));
            $st->setQuantity(rand(1, 100));
            $st->setActive(true);
            $manager->persist($st);
            $this->addReference('stock-' . $s, $st);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['StockFixtures'];
    }
}
