<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Task;
use Doctrine\DBAL\Connection;

class ProjectRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return List<Project>
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM projects ORDER BY id DESC";
        $projectsData = $this->connection->fetchAllAssociative($sql);

        $projects = [];
        foreach ($projectsData as $row) {
            $projects[] = new Project($row['id'], $row['title'], $row['description'], $row['budget']);
        }
        return $projects;
    }

    public function find(int $id): ?Project
    {
        $sql = "SELECT * FROM projects WHERE id = ?";
        $row = $this->connection->fetchAssociative($sql, [$id]);

        return $row ? new Project($row['id'], $row['title'], $row['description'], $row['budget']) : null;
    }

    public function insert(Project $project): bool
    {
        $sql = "INSERT INTO projects (title, description, budget) VALUES (:title, :description, :budget)";
        return $this->connection->executeStatement($sql, [
                'title' => $project->title,
                'description' => $project->description,
                'budget' => $project->budget,
            ]) > 0;
    }

    public function update(int $id, Project $project): bool
    {
        $sql = "UPDATE projects SET title = :title, description = :description, budget = :budget WHERE id = :id";
        return $this->connection->executeStatement($sql, [
                'id' => $id,
                'title' => $project->title,
                'description' => $project->description,
                'budget' => $project->budget,
            ]) > 0;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM projects WHERE id = :id";
        return $this->connection->executeStatement($sql, ['id' => $id]) > 0;
    }
}
