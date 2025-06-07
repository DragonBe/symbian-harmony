<?php
declare(strict_types=1);

namespace App\Model;

final class Course
{
    private int $id;
    private string $title;
    private string $description;
    private bool $isPromoted;
    private bool $isArchived;
    private \DateTimeImmutable $createdAt;
    private ?\DateTimeImmutable $updatedAt;

    public function __construct(
        int $id,
        string $title,
        string $description,
        bool $isPromoted,
        bool $isArchived,
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->isPromoted = $isPromoted;
        $this->isArchived = $isArchived;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isPromoted(): bool
    {
        return $this->isPromoted;
    }

    public function isArchived(): bool
    {
        return $this->isArchived;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    // Since we're using immutable models, we need to create new instances for updates
    public function withTitle(string $title): self
    {
        $clone = clone $this;
        $clone->title = $title;
        $clone->updatedAt = new \DateTimeImmutable();
        return $clone;
    }

    public function withDescription(string $description): self
    {
        $clone = clone $this;
        $clone->description = $description;
        $clone->updatedAt = new \DateTimeImmutable();
        return $clone;
    }

    public function withPromoted(bool $isPromoted): self
    {
        $clone = clone $this;
        $clone->isPromoted = $isPromoted;
        $clone->updatedAt = new \DateTimeImmutable();
        return $clone;
    }

    public function withArchived(bool $isArchived): self
    {
        $clone = clone $this;
        $clone->isArchived = $isArchived;
        $clone->updatedAt = new \DateTimeImmutable();
        return $clone;
    }

    // Factory method to create a new course
    public static function create(
        string $title,
        string $description,
        bool $isPromoted = false,
        bool $isArchived = false
    ): self {
        return new self(
            0, // ID will be set by the database
            $title,
            $description,
            $isPromoted,
            $isArchived,
            new \DateTimeImmutable(),
            null
        );
    }

    // Factory method to create from database row
    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['id'],
            $data['title'],
            $data['description'],
            (bool) $data['is_promoted'],
            (bool) $data['is_archived'],
            new \DateTimeImmutable($data['created_at']),
            $data['updated_at'] ? new \DateTimeImmutable($data['updated_at']) : null
        );
    }

    // Convert to array for database storage
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'is_promoted' => $this->isPromoted,
            'is_archived' => $this->isArchived,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null,
        ];
    }
}