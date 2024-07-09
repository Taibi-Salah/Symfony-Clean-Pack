<?php

// src/Entity/Intervention.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InterventionRepository")
 * @ORM\Table(name="interventions")
 */
class Intervention
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ticket")
     * @ORM\JoinColumn(nullable=false)
     */
    private Ticket $ticket;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Technicien")
     * @ORM\JoinColumn(nullable=false)
     */
    private Technicien $technicien;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    public function setTicket(Ticket $ticket): self
    {
        $this->ticket = $ticket;
        return $this;
    }

    public function getTechnicien(): Technicien
    {
        return $this->technicien;
    }

    public function setTechnicien(Technicien $technicien): self
    {
        $this->technicien = $technicien;
        return $this;
    }
}

