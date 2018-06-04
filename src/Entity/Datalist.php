<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DatalistRepository")
 */
class Datalist
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="datalists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LdapUser", inversedBy="datalists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Device", inversedBy="datalists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $device;

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getUser(): ?LdapUser
    {
        return $this->user;
    }

    public function setUser(?LdapUser $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDevice(): ?Device
    {
        return $this->device;
    }

    public function setDevice(?Device $device): self
    {
        $this->device = $device;

        return $this;
    }
}