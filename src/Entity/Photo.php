<?php

/*
 This work, including the code samples, is licensed under a Creative Commons BY-SA 3.0 license.
 */

namespace App\Entity;

use App\Repository\PhotosRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Photo.
 *
 * Represents a photo entity, including title, text, filename, and gallery.
 */
#[ORM\Entity(repositoryClass: PhotosRepository::class)]
#[ORM\Table(name: 'photos')]
class Photo
{
    /**
     * Primary key.
     *
     * @var int|null ID of the photo
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Creation timestamp.
     *
     * @var \DateTimeInterface Creation date and time of the photo
     */
    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    private \DateTimeInterface $createdAt;

    /**
     * Update timestamp.
     *
     * @var \DateTimeInterface|null Last update date and time of the photo
     */
    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * Title of the photo.
     */
    #[ORM\Column(type: 'string', length: 64)]
    #[Assert\NotBlank(
        message: 'Photo title is required.',
        normalizer: 'trim'
    )]
    #[Assert\Length(min: 3, max: 64)]
    private ?string $title = null;

    /**
     * Text description of the photo.
     */
    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(
        message: 'Photo description is required.',
        normalizer: 'trim'
    )]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $text = null;

    /**
     * Filename of the photo.
     */
    #[ORM\Column(type: 'string', length: 191)]
    #[Assert\Type(type: 'string')]
    private ?string $filename = null;

    /**
     * Galleries the photo belongs to.
     */
    #[ORM\ManyToOne(targetEntity: Gallery::class)]
    #[ORM\JoinColumn(name: 'gallery_id', referencedColumnName: 'id')]
    private ?Gallery $gallery = null;

    /**
     * Photo constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get the photo ID.
     *
     * @return int|null ID of the photo
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the creation timestamp.
     *
     * @return \DateTimeInterface Creation date and time of the photo
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set the creation timestamp.
     *
     * @param \DateTimeInterface $createdAt Creation date and time
     *
     * @return self Returns the current Photo instance
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the update timestamp.
     *
     * @return \DateTimeInterface|null Last update date and time of the photo
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Set the update timestamp.
     *
     * @param \DateTimeInterface $updatedAt Last update date and time
     *
     * @return self Returns the current Photo instance
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the photo title.
     *
     * @return string|null Title of the photo
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the photo title.
     *
     * @param string $title Title of the photo
     *
     * @return self Returns the current Photo instance
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the photo text description.
     *
     * @return string|null Text description of the photo
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * Set the photo text description.
     *
     * @param string|null $text Text description of the photo
     *
     * @return self Returns the current Photo instance
     */
    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get the gallery.
     *
     * @return Gallery|null Galleries entity the photo belongs to
     */
    public function getGallery(): ?Gallery
    {
        return $this->gallery;
    }

    /**
     * Set the gallery.
     *
     * @param Gallery|null $gallery Galleries entity
     *
     * @return self Returns the current Photo instance
     */
    public function setGallery(?Gallery $gallery): self
    {
        $this->gallery = $gallery;

        return $this;
    }

    /**
     * Get the filename.
     *
     * @return string|null Filename of the photo
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Set the filename.
     *
     * @param string $filename Filename of the photo
     *
     * @return self Returns the current Photo instance
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }
}
