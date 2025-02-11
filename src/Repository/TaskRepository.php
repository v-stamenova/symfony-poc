<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\DBAL\Connection;

class TaskRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return List<Task>
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM tasks ORDER BY id DESC";
        $tasksData = $this->connection->fetchAllAssociative($sql);

        $tasks = [];
        foreach ($tasksData as $row) {
            $tasks[] = new Task($row['id'], $row['title'], $row['description'], $row['project_id']);
        }
        return $tasks;
    }

    public function find(int $id): ?Task
    {
        $sql = "SELECT * FROM tasks WHERE id = ?";
        $row = $this->connection->fetchAssociative($sql, [$id]);

        return $row ? new Task($row['id'], $row['title'], $row['description'], $row['project_id']) : null;
    }

    public function insert(Task $task): bool
    {
        $sql = "INSERT INTO tasks (title, description, project_id) VALUES (:title, :description, :project_id)";
        return $this->connection->executeStatement($sql, [
                'title' => $task->title,
                'description' => $task->description,
                'project_id' => $task->project_id
            ]) > 0;
    }

    public function update(int $id, Task $task): bool
    {
        $sql = "UPDATE tasks SET title = :title, description = :description, project_id = :project_id WHERE id = :id";
        return $this->connection->executeStatement($sql, [
                'id' => $id,
                'title' => $task->title,
                'description' => $task->description,
                'project_id' => $task->project_id,
            ]) > 0;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM tasks WHERE id = :id";
        return $this->connection->executeStatement($sql, ['id' => $id]) > 0;
    }

    /**
     * @return List<Task>
     */
    public function findAllByProject(int $projectId): array {
        $sql = "SELECT * FROM tasks WHERE project_id = ?";

        $tasksData = $this->connection->fetchAllAssociative($sql, [$projectId]);

        $tasks = [];
        foreach ($tasksData as $row) {
            $tasks[] = new Task($row['id'], $row['title'], $row['description'], $row['project_id']);
        }
        return $tasks;
    }
}