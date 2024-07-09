<?php

// src/Entity/InterventionStock.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InterventionStockRepository")
 * @ORM\Table(name="intervention_stocks")
 */
class InterventionStock
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="integer")
     */
    private int $quantity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Intervention")
     * @ORM\JoinColumn(nullable=false)
     */
    private Intervention $intervention;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Stock")
     * @ORM\JoinColumn(nullable=false)
     */
    private Stock $stock;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getIntervention(): Intervention
    {
        return $this->intervention;
    }

    public function setIntervention(Intervention $intervention): self
    {
        $this->intervention = $intervention;
        return $this;
    }

    public function getStock(): Stock
    {
        return $this->stock;
    }

    public function setStock(Stock $stock): self
    {
        $this->stock = $stock;
        return $this;
    }
}

