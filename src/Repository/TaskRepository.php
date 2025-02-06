<?php

namespace App\Repository;

use App\Entity\Task;
use App\Repository\Interfaces\TaskRepositoryInterface;
use Doctrine\DBAL\Connection;
use PDO;

class TaskRepository implements TaskRepositoryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM tasks ORDER BY id DESC";
        $tasksData = $this->connection->fetchAllAssociative($sql);

        $tasks = [];
        foreach ($tasksData as $row) {
            $tasks[] = new Task($row['id'], $row['title'], $row['description']);
        }
        return $tasks;
    }

    public function find(int $id): ?Task
    {
        $sql = "SELECT * FROM tasks WHERE id = ?";
        $row = $this->connection->fetchAssociative($sql, [$id]);

        return $row ? new Task($row['id'], $row['title'], $row['description']) : null;
    }

    public function insert(Task $task): bool
    {
        $sql = "INSERT INTO tasks (title, description) VALUES (:title, :description)";
        return $this->connection->executeStatement($sql, [
                'title' => $task->title,
                'description' => $task->description
            ]) > 0;
    }

    public function update(int $id, Task $task): bool
    {
        $sql = "UPDATE tasks SET title = :title, description = :description WHERE id = :id";
        return $this->connection->executeStatement($sql, [
                'id' => $id,
                'title' => $task->title,
                'description' => $task->description
            ]) > 0;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM tasks WHERE id = :id";
        return $this->connection->executeStatement($sql, ['id' => $id]) > 0;
    }

}