<?php

namespace App\Entity;

use App\Repository\NotionPageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotionPageRepository::class)
 */
class NotionPage
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $notionId;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\OneToMany(targetEntity=SendEmail::class, mappedBy="notionPage", orphanRemoval=true)
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getNotionId(): ?string
    {
        return $this->notionId;
    }

    public function setNotionId(string $notionId): self
    {
        $this->notionId = $notionId;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

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
            $sendEmail->setNotionPage($this);
        }

        return $this;
    }

    public function removeSendEmail(SendEmail $sendEmail): self
    {
        if ($this->sendEmails->removeElement($sendEmail)) {
            // set the owning side to null (unless already changed)
            if ($sendEmail->getNotionPage() === $this) {
                $sendEmail->setNotionPage(null);
            }
        }

        return $this;
    }
}
