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

    #[ORM\Column(length: 255)]
    private ?string $operatingSystem = null;

    #[ORM\Column(length: 255)]
    private ?string $cellularTechnology = null;

    #[ORM\Column(length: 255)]
    private ?int $memoryStorage = null;

    #[ORM\Column(length: 255)]
    private ?string $connectivityTechnoloy = null;

    #[ORM\Column(length: 255)]
    private ?int $screenSize = null;

    #[ORM\Column(length: 255)]
    private ?string $wirelessNetworkTechnology = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $releaseDate = null;

    #[ORM\Column]
    private ?int $batteryAutonomy = null;

    #[ORM\Column]
    private ?int $ramSize = null;

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

    public function getOperatingSystem(): ?string
    {
        return $this->operatingSystem;
    }

    public function setOperatingSystem(string $operatingSystem): static
    {
        $this->operatingSystem = $operatingSystem;

        return $this;
    }

    public function getCellularTechnology(): ?string
    {
        return $this->cellularTechnology;
    }

    public function setCellularTechnology(string $cellularTechnology): static
    {
        $this->cellularTechnology = $cellularTechnology;

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

    public function getConnectivityTechnoloy(): ?string
    {
        return $this->connectivityTechnoloy;
    }

    public function setConnectivityTechnoloy(string $connectivityTechnoloy): static
    {
        $this->connectivityTechnoloy = $connectivityTechnoloy;

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

    public function getWirelessNetworkTechnology(): ?string
    {
        return $this->wirelessNetworkTechnology;
    }

    public function setWirelessNetworkTechnology(string $wirelessNetworkTechnology): static
    {
        $this->wirelessNetworkTechnology = $wirelessNetworkTechnology;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getBatteryAutonomy(): ?int
    {
        return $this->batteryAutonomy;
    }

    public function setBatteryAutonomy(int $batteryAutonomy): static
    {
        $this->batteryAutonomy = $batteryAutonomy;

        return $this;
    }

    public function getRamSize(): ?int
    {
        return $this->ramSize;
    }

    public function setRamSize(int $ramSize): static
    {
        $this->ramSize = $ramSize;

        return $this;
    }
}
