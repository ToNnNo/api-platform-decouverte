<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use App\Repository\PostRepository;
use App\State\PostStateProcessor;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    // shortName: "Articles",
    description: 'Hello World',
    operations: [
        new Get(
            // uriTemplate: '/article/{id}.{_format}',
        ),
        new GetCollection(
            normalizationContext: ['groups' => 'read:post:collection']
        ),
        new \ApiPlatform\Metadata\Post(),
        new Put(
            validationContext: ['groups' => ['Default', 'validation:post:update']],
            processor: PostStateProcessor::class
        ),
        new Patch(
            validationContext: ['groups' => ['Default', 'validation:post:update']],
            processor: PostStateProcessor::class
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => 'read:post:item'],
    denormalizationContext: ['groups' => 'write:post:item']
)]
// #[ApiFilter(SearchFilter::class, properties: ['title' => 'partial'])]
class Post
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['read:post:collection', 'read:post:item'])]
    private ?int $id = null;

    /**
     * Titre de l'article
     */
    #[ORM\Column(length: 255)]
    #[Groups(['read:post:collection', 'read:post:item', 'write:post:item'])]
    #[Assert\NotBlank()]
    #[ApiFilter(SearchFilter::class, strategy: 'word_start')]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:post:collection', 'read:post:item', 'write:post:item'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read:post:item', 'write:post:item'])]
    #[Assert\NotBlank(groups: ['validation:post:update'])]
    private ?string $content = null;

    #[ORM\Column]
    // #[Groups(['read:post:item'])]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[Groups(['read:post:collection', 'read:post:item', 'write:post:item'])]
    private ?Category $category = null;

    #[ORM\Column(options: ['default' => 0])]
    #[Groups(['read:post:collection', 'read:post:item', 'write:post:item'])]
    private ?bool $published = false;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(['read:post:item'])]
    public function getCreatedAtAgo(): string
    {
        return Carbon::instance($this->createdAt)->diffForHumans();
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }
}
