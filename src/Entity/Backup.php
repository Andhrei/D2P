<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BackupRepository")
 */
class Backup
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
     * @ORM\ManyToOne(targetEntity="App\Entity\LdapUser", inversedBy="backups")
     */
    private $user;

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

    public function getUser(): ?LdapUser
    {
        return $this->user;
    }

    public function setUser(?LdapUser $user): self
    {
        $this->user = $user;

        return $this;
    }
}
