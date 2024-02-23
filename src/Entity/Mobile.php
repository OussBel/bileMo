<?php

namespace App\Entity;

use App\Repository\MobileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MobileRepository::class)]
class Mobile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[ORM\Column(length: 255)]
    private ?string $modelName = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $memoryStorage = null;

    #[ORM\Column]
    private ?int $screenSize = null;

    #[ORM\Column(length: 255)]
    private ?string $wirelessNetwork = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModelName(): ?string
    {
        return $this->modelName;
    }

    public function setModelName(string $modelName): static
    {
        $this->modelName = $modelName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMemoryStorage(): ?int
    {
        return $this->memoryStorage;
    }

    public function setMemoryStorage(int $memoryStorage): static
    {
        $this->memoryStorage = $memoryStorage;

        return $this;
    }

    public function getScreenSize(): ?int
    {
        return $this->screenSize;
    }

    public function setScreenSize(int $screenSize): static
    {
        $this->screenSize = $screenSize;

        return $this;
    }

    public function getWirelessNetwork(): ?string
    {
        return $this->wirelessNetwork;
    }

    public function setWirelessNetwork(string $wirelessNetwork): static
    {
        $this->wirelessNetwork = $wirelessNetwork;

        return $this;
    }
}
