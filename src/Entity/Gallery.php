<?php

/*
 This work, including the code samples, is licensed under a Creative Commons BY-SA 3.0 license.
 */

namespace App\Entity;

use App\Repository\GalleriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Gallery.
 *
 * Represents a gallery which contains multiple photos.
 */
#[ORM\Entity(repositoryClass: GalleriesRepository::class)]
#[ORM\Table(name: 'galleries')]
class Gallery
{
    /**
     * Primary key (Gallery ID).
     *
     * @var int|null The unique identifier of the gallery
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Creation timestamp.
     *
     * @var \DateTimeInterface|null The date and time when the gallery was created
     */
    #[ORM\Column(type: 'datetime')]
    #[Assert\Type(type: \DateTimeInterface::class)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeInterface $createdAt = null;

    /**
     * Last update timestamp.
     *
     * @var \DateTimeInterface|null The date and time when the gallery was last updated
     */
    #[ORM\Column(type: 'datetime')]
    #[Assert\Type(type: \DateTimeInterface::class)]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * Gallery title.
     *
     * @var string|null Human-readable title of the gallery
     */
    #[ORM\Column(type: 'string', length: 64)]
    #[Assert\Type(type: 'string')]
    #[Assert\NotBlank(message: 'Gallery title is required.', normalizer: 'trim')]
    #[Assert\Length(min: 3, max: 64)]
    private ?string $title = null;

    /**
     * Photos contained in this gallery.
     *
     * @var Collection<int, Photo> Collection of Photo objects associated with this gallery
     */
    #[ORM\OneToMany(mappedBy: 'gallery', targetEntity: Photo::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $photos;

    /**
     * Slug code derived from title.
     *
     * @var string|null Slug representation of the gallery title
     */
    #[ORM\Column(type: 'string', length: 64)]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 3, max: 64)]
    #[Gedmo\Slug(fields: ['title'])]
    private ?string $code = null;

    /**
     * Gallery constructor.
     *
     * Initializes the photos collection.
     */
    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

    /**
     * Get the gallery ID.
     *
     * @return int|null Returns the unique identifier of the gallery
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the creation timestamp.
     *
     * @return \DateTimeInterface|null Returns the creation timestamp
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set the creation timestamp.
     *
     * @param \DateTimeInterface $createdAt Creation timestamp
     *
     * @return self Returns the current Gallery instance
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the last update timestamp.
     *
     * @return \DateTimeInterface|null Returns the last update timestamp
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Set the last update timestamp.
     *
     * @param \DateTimeInterface $updatedAt Last update timestamp
     *
     * @return self Returns the current Gallery instance
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the gallery title.
     *
     * @return string|null Returns the gallery title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the gallery title.
     *
     * @param string|null $title The gallery title
     *
     * @return self Returns the current Gallery instance
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get all photos in the gallery.
     *
     * @return Collection<int, Photo> Returns a collection of Photo entities
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    /**
     * Add a photo to the gallery.
     *
     * @param Photo $photo Photo entity to add
     *
     * @return self Returns the current Gallery instance
     */
    public function addPhoto(Photo $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setGallery($this);
        }

        return $this;
    }

    /**
     * Remove a photo from the gallery.
     *
     * @param Photo $photo Photo entity to remove
     *
     * @return self Returns the current Gallery instance
     */
    public function removePhoto(Photo $photo): self
    {
        if ($this->photos->removeElement($photo)) {
            if ($photo->getGallery() === $this) {
                $photo->setGallery(null);
            }
        }

        return $this;
    }

    /**
     * Get the slug code of the gallery.
     *
     * @return string|null Returns the slug code
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Set the slug code of the gallery.
     *
     * @param string $code Slug code to set
     *
     * @return self Returns the current Gallery instance
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
