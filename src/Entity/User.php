<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity=SendEmail::class, mappedBy="uuser", orphanRemoval=true)
     */
    private $sendEmails;

    public function __construct()
    {
        $this->sendEmails = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|SendEmail[]
     */
    public function getSendEmails(): Collection
    {
        return $this->sendEmails;
    }

    public function addSendEmail(SendEmail $sendEmail): self
    {
        if (!$this->sendEmails->contains($sendEmail)) {
            $this->sendEmails[] = $sendEmail;
            $sendEmail->setUuser($this);
        }

        return $this;
    }

    public function removeSendEmail(SendEmail $sendEmail): self
    {
        if ($this->sendEmails->removeElement($sendEmail)) {
            // set the owning side to null (unless already changed)
            if ($sendEmail->getUuser() === $this) {
                $sendEmail->setUuser(null);
            }
        }

        return $this;
    }
}
