<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeviceRepository")
 */
class Device
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $library;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LdapUser", inversedBy="devices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Schedule", mappedBy="device")
     */
    private $schedules;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Datalist", mappedBy="device")
     */
    private $datalists;

    public function __construct()
    {
        $this->schedules = new ArrayCollection();
        $this->datalists = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getLibrary(): ?string
    {
        return $this->library;
    }

    public function setLibrary(?string $library): self
    {
        $this->library = $library;

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

    /**
     * @return Collection|Schedule[]
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(Schedule $schedule): self
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules[] = $schedule;
            $schedule->setDevice($this);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): self
    {
        if ($this->schedules->contains($schedule)) {
            $this->schedules->removeElement($schedule);
            // set the owning side to null (unless already changed)
            if ($schedule->getDevice() === $this) {
                $schedule->setDevice(null);
            }
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
            $datalist->setDevice($this);
        }

        return $this;
    }

    public function removeDatalist(Datalist $datalist): self
    {
        if ($this->datalists->contains($datalist)) {
            $this->datalists->removeElement($datalist);
            // set the owning side to null (unless already changed)
            if ($datalist->getDevice() === $this) {
                $datalist->setDevice(null);
            }
        }

        return $this;
    }
}
