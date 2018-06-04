<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 */
class Client
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $hostname;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\LdapUser", inversedBy="clients")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Datalist", mappedBy="client")
     */
    private $datalists;


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->datalists = new ArrayCollection();
    }

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

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function setHostname(string $hostname): self
    {
        $this->hostname = $hostname;

        return $this;
    }

    public function getShortName()
    {
        return explode('.',$this->hostname)[0];
    }

    /**
     * @return Collection|LdapUser[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(LdapUser $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(LdapUser $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }

    /**
     * @return Collection|Datalist[]
     */
    public function getDatalists(): Collection
    {
        return $this->datalists;
    }

    public function addDatalist(Datalist $datalist): self
    {
        if (!$this->datalists->contains($datalist)) {
            $this->datalists[] = $datalist;
            $datalist->setClient($this);
        }

        return $this;
    }

    public function removeDatalist(Datalist $datalist): self
    {
        if ($this->datalists->contains($datalist)) {
            $this->datalists->removeElement($datalist);
            // set the owning side to null (unless already changed)
            if ($datalist->getClient() === $this) {
                $datalist->setClient(null);
            }
        }

        return $this;
    }
}
