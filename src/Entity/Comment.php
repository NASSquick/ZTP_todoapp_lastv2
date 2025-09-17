<?php

/*
 This work, including the code samples, is licensed under a Creative Commons BY-SA 3.0 license.
 */

namespace App\Entity;

use App\Repository\CommentsRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Comment.
 *
 * Represents a comment on a photo, including email, nickname, text, and related photo.
 */
#[ORM\Entity(repositoryClass: CommentsRepository::class)]
#[ORM\Table(name: 'comments')]
class Comment
{
    /**
     * Primary key.
     *
     * @var int|null ID of the comment
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Creation timestamp.
     *
     * @var \DateTimeInterface Creation date and time of the comment
     */
    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    private \DateTimeInterface $createdAt;

    /**
     * Update timestamp.
     *
     * @var \DateTimeInterface|null Last update date and time of the comment
     */
    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * Email of the comment author.
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Email is required.', normalizer: 'trim')]
    #[Assert\Email(message: 'Please provide a valid email address.')]
    private ?string $email = null;

    /**
     * Nickname of the comment author.
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Nickname is required.', normalizer: 'trim')]
    private ?string $nick = null;

    /**
     * Text of the comment.
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Comment text is required.', normalizer: 'trim')]
    private ?string $text = null;

    /**
     * Related photo.
     *
     * Unidirectional ManyToOne: a comment belongs to a photo.
     */
    #[ORM\ManyToOne(targetEntity: Photo::class)]
    #[ORM\JoinColumn(name: 'photo_id', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotNull(message: 'Photo selection is required.')]
    private ?Photo $photo = null;

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get the comment ID.
     *
     * @return int|null ID of the comment
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the creation timestamp.
     *
     * @return \DateTimeInterface Creation date and time of the comment
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
     * @return self Returns the current Comment instance
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the update timestamp.
     *
     * @return \DateTimeInterface|null Last update date and time of the comment
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
     * @return self Returns the current Comment instance
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get the author's email.
     *
     * @return string|null Email of the comment author
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the author's email.
     *
     * @param string $email Email of the comment author
     *
     * @return self Returns the current Comment instance
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the author's nickname.
     *
     * @return string|null Nickname of the comment author
     */
    public function getNick(): ?string
    {
        return $this->nick;
    }

    /**
     * Set the author's nickname.
     *
     * @param string $nick Nickname of the comment author
     *
     * @return self Returns the current Comment instance
     */
    public function setNick(string $nick): self
    {
        $this->nick = $nick;

        return $this;
    }

    /**
     * Get the comment text.
     *
     * @return string|null Text content of the comment
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * Set the comment text.
     *
     * @param string $text Text content of the comment
     *
     * @return self Returns the current Comment instance
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get the related photo.
     *
     * @return Photo|null The Photo entity this comment belongs to
     */
    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    /**
     * Set the related photo.
     *
     * @param Photo|null $photo The Photo entity to relate this comment to
     *
     * @return self Returns the current Comment instance
     */
    public function setPhoto(?Photo $photo): self
    {
        $this->photo = $photo;

        return $this;
    }
}
