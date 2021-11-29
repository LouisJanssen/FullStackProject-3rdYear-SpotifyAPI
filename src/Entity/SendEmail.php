<?php

namespace App\Entity;

use App\Repository\SendEmailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SendEmailRepository::class)
 */
class SendEmail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="sendEmails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $uuser;

    /**
     * @ORM\ManyToOne(targetEntity=NotionPage::class, inversedBy="sendEmails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $notionPage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuser(): ?User
    {
        return $this->uuser;
    }

    public function setUuser(?User $uuser): self
    {
        $this->uuser = $uuser;

        return $this;
    }

    public function getNotionPage(): ?NotionPage
    {
        return $this->notionPage;
    }

    public function setNotionPage(?NotionPage $notionPage): self
    {
        $this->notionPage = $notionPage;

        return $this;
    }
}
