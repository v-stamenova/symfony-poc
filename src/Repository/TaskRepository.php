<?php

namespace App\Repository;

use App\Entity\Task;
use App\Repository\Interfaces\TaskRepositoryInterface;
use PDO;

class TaskRepository implements TaskRepositoryInterface
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('sqlite:' . __DIR__ . '/../../var/data/db.sqlite');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @return Task[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM tasks ORDER BY id DESC");
        $tasks = [];

        // PHPStan does not like it when you try to fetch on PDOStatement|false (who would've thought)
        if ($stmt !== false) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if (is_array($row)) {
                    $tasks[] = new Task(
                        is_int($row['id']) ? $row['id'] : (is_numeric($row['id']) ? (int) $row['id'] : 0),
                        is_string($row['title']) ? $row['title'] : (is_scalar($row['title']) ? strval($row['title']) : ''),
                        is_string($row['description']) ? $row['description'] : (is_scalar($row['description']) ? strval($row['description']) : '')
                    );
                }
            }
        }

        return $tasks;
    }

    public function find(int $id): ?Task
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (is_array($row)) {
            $task = new Task(
                is_int($row['id']) ? $row['id'] : (is_numeric($row['id']) ? (int) $row['id'] : 0),
                is_string($row['title']) ? $row['title'] : (is_scalar($row['title']) ? strval($row['title']) : ''),
                is_string($row['description']) ? $row['description'] : (is_scalar($row['description']) ? strval($row['description']) : '')
            );

            return $task;
        }

        return null;
    }

    public function insert(Task $task): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO tasks (title, description) VALUES (?, ?)");
        return $stmt->execute([$task->title, $task->description]);
    }

    public function update(int $id, Task $task): bool
    {
        $stmt = $this->pdo->prepare("UPDATE tasks SET title = ?, description = ? WHERE id = ?");
        return $stmt->execute([$task->title, $task->description, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM tasks WHERE id = ?");
        return $stmt->execute([$id]);
    }

}