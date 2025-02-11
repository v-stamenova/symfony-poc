<?php

namespace App\Entity;

class Project
{
    public ?int $id;

    public string $title;

    public ?string $description;

    public int $budget;

    public function __construct(?int $id, string $title, ?string $description, int $budget)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->budget = $budget;
    }

    /**
     * @return string[]
     */
    public static function validation(string $title, string $description, int $budget): array
    {
        $errors = [];

        if (empty($title)) {
            $errors[] = "Title is required.";
        } elseif (strlen($title) < 3 || strlen($title) > 255) {
            $errors[] = "Title must be between 3 and 255 characters.";
        }

        if (!empty($description) && strlen($description) > 1000) {
            $errors[] = "Description cannot exceed 1000 characters.";
        }

        if (empty($budget)) {
            $errors[] = "Budget is required.";
        } elseif ($budget <= 0) {
            $errors[] = "Budget must be greater than 0.";
        }

        return $errors;
    }
}
