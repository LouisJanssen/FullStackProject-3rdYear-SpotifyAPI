<?php

namespace App\Entity;

use App\Repository\UserRepository;
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
    private $accessToken;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $userId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $userImageUrl;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $frontToken;

    /**
     * @ORM\Column(type="boolean")
     */
    private $alreadyVoted;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
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

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUserImageUrl(): ?string
    {
        return $this->userImageUrl;
    }

    public function setUserImageUrl(string $userImageUrl): self
    {
        $this->userImageUrl = $userImageUrl;

        return $this;
    }

    public function getFrontToken(): ?string
    {
        return $this->frontToken;
    }

    public function setFrontToken(string $frontToken): self
    {
        $this->frontToken = $frontToken;

        return $this;
    }

    public function getAlreadyVoted(): ?bool
    {
        return $this->alreadyVoted;
    }

    public function setAlreadyVoted(bool $alreadyVoted): self
    {
        $this->alreadyVoted = $alreadyVoted;

        return $this;
    }
}
