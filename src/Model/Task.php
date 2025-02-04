<?php

namespace App\Model;
use Symfony\Component\Validator\Constraints as Assert;

class Task
{
    public ?int $id;

    #[Assert\NotBlank(message: "Title is required")]
    #[Assert\Length(min: 3, max: 255, minMessage: "Title must be at least 3 characters long", maxMessage: "Title cannot be longer than 255 characters")]
    public string $title;

    #[Assert\Length(max: 1000, maxMessage: "Description cannot exceed 1000 characters")]
    public ?string $description;

    public function __construct(?int $id, string $title, ?string $description)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
    }
}