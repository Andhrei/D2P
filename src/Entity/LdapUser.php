<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LdapUserRepository")
 * @UniqueEntity(fields="id", message="Id already taken")
 * @UniqueEntity(fields="username", message="username already taken")
 * @UniqueEntity(fields="email", message="email already taken")
 */
class LdapUser implements UserInterface, EquatableInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=254, nullable=true, unique=true)
     *
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $roles;

    /**
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $displayName;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Client", mappedBy="users")
     */
    private $clients;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Schedule", mappedBy="user", orphanRemoval=true)
     */
    private $schedules;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Device", mappedBy="user")
     */
    private $devices;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Datalist", mappedBy="user")
     */
    private $datalists;

    public function __construct(Entry $entry)
    {
        $this->username = $entry->getAttribute('sAMAccountName')[0];
        $this->displayName = $entry->getAttribute('displayName')[0];

//        $this->password = '...';
//        $this->salt = '...';
        $this->setIsActive(true);
        $this->clients = new ArrayCollection();
        $this->schedules = new ArrayCollection();
        $this->devices = new ArrayCollection();
        $this->datalists = new ArrayCollection();
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function serialize()
    {
        return serialize(array(
            $this->displayName,
            $this->username,
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->displayName,
            $this->username,
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }


    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if(!$user instanceof LdapUser)
        {
            return false;
        }

        if ($this->password !== $user->getPassword())
        {
            return false;
        }

        if ($this->salt !== $user->getSalt())
        {
            return false;
        }

        if ($this->username !== $user->getUsername())
        {
            return false;
        }

        return true;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @return Collection|Client[]
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->addUser($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->contains($client)) {
            $this->clients->removeElement($client);
            $client->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection|Backup[]
     */
    public function getBackups(): Collection
    {
        return $this->backups;
    }

    /**
     * @return Collection|Schedule[]
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSpecification(Schedule $specification): self
    {
        if (!$this->schedules->contains($specification)) {
            $this->schedules[] = $specification;
            $specification->setUser($this);
        }

        return $this;
    }

    public function removeSpecification(Schedule $specification): self
    {
        if ($this->schedules->contains($specification)) {
            $this->schedules->removeElement($specification);
            // set the owning side to null (unless already changed)
            if ($specification->getUser() === $this) {
                $specification->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Device[]
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function getAzureDevice()
    {
        foreach ($this->devices as $device)
        {
            if($device->getType() == "Azure")
            {
                return $device;
            }
        }

        return null;
    }

    public function getLocalDevice()
    {
        foreach ($this->devices as $device)
        {
            if($device->getType() == "Local")
            {
                return $device;
            }
        }

        return null;
    }

    public function addDevice(Device $device): self
    {
        if (!$this->devices->contains($device)) {
            $this->devices[] = $device;
            $device->setUser($this);
        }

        return $this;
    }

    public function removeDevice(Device $device): self
    {
        if ($this->devices->contains($device)) {
            $this->devices->removeElement($device);
            // set the owning side to null (unless already changed)
            if ($device->getUser() === $this) {
                $device->setUser(null);
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
            $datalist->setUser($this);
        }

        return $this;
    }

    public function removeDatalist(Datalist $datalist): self
    {
        if ($this->datalists->contains($datalist)) {
            $this->datalists->removeElement($datalist);
            // set the owning side to null (unless already changed)
            if ($datalist->getUser() === $this) {
                $datalist->setUser(null);
            }
        }

        return $this;
    }
}
