<?php

namespace App\Entity;

use App\Repository\TrackRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TrackRepository::class)
 */
class Track
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
    private $trackId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $trackName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $previewUrl;

    /**
     * @ORM\ManyToOne(targetEntity=Artist::class, inversedBy="track")
     * @ORM\JoinColumn(nullable=false)
     */
    private $artist;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrackId(): ?string
    {
        return $this->trackId;
    }

    public function setTrackId(string $trackId): self
    {
        $this->trackId = $trackId;

        return $this;
    }

    public function getTrackName(): ?string
    {
        return $this->trackName;
    }

    public function setTrackName(string $trackName): self
    {
        $this->trackName = $trackName;

        return $this;
    }

    public function getPreviewUrl(): ?string
    {
        return $this->previewUrl;
    }

    public function setPreviewUrl(?string $previewUrl): self
    {
        $this->previewUrl = $previewUrl;

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): self
    {
        $this->artist = $artist;

        return $this;
    }
}