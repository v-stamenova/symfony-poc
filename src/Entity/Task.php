<?php

namespace App\Entity;

class Task
{
    public ?int $id;
  
    public string $title;

    public ?string $description;

    public ?int $project_id;

    public function __construct(?int $id, string $title, ?string $description, ?int $project_id)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->project_id = $project_id;
    }

    /**
     * @return string[]
     */
    public static function validation(string $title, string $description): array {
        $errors = [];

        if (empty($title)) {
            $errors[] = "Title is required.";
        } elseif (strlen($title) < 3 || strlen($title) > 255) {
            $errors[] = "Title must be between 3 and 255 characters.";
        }

        if (!empty($description) && strlen($description) > 1000) {
            $errors[] = "Description cannot exceed 1000 characters.";
        }

        return $errors;
    }
}