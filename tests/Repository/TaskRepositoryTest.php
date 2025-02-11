<?php

namespace App\Tests\Repository;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

class TaskRepositoryTest extends TestCase
{
    private Connection $connection;
    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        $this->connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]);

        $this->connection->executeStatement("
            CREATE TABLE tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT
            )
        ");

        $this->taskRepository = new TaskRepository($this->connection);
    }

    public function testFindAll(): void
    {
        $this->connection->insert('tasks', ['title' => 'Task 1', 'description' => 'Description 1']);
        $this->connection->insert('tasks', ['title' => 'Task 2', 'description' => 'Description 2']);

        $tasks = $this->taskRepository->findAll();

        $this->assertCount(2, $tasks);
        $this->assertEquals('Task 2', $tasks[0]->title);
        $this->assertEquals('Task 1', $tasks[1]->title);
    }

    public function testFind(): void
    {
        $this->connection->insert('tasks', [
            'title' => 'Find Me',
            'description' => 'Testing find method'
        ]);

        $task = $this->taskRepository->find(1);

        $this->assertNotNull($task);
        $this->assertEquals('Find Me', $task->title);
        $this->assertEquals('Testing find method', $task->description);
    }

    public function testInsert(): void
    {
        $task = new Task(1, "Test Task", "This is a test task");
        $result = $this->taskRepository->insert($task);

        $stmt = $this->connection->executeQuery("SELECT * FROM tasks WHERE id = ?", [1]);
        $row = $stmt->fetchAssociative();

        $this->assertTrue($result);
        $this->assertNotFalse($row);
        $this->assertEquals("Test Task", $row['title']);
        $this->assertEquals("This is a test task", $row['description']);
    }

    public function testUpdate(): void
    {
        $this->connection->insert('tasks', ['title' => 'Old Title', 'description' => 'Old Description']);

        $task = new Task(1, "Updated Title", "Updated Description");
        $result = $this->taskRepository->update(1, $task);

        $stmt = $this->connection->executeQuery("SELECT * FROM tasks WHERE id = ?", [1]);
        $row = $stmt->fetchAssociative();

        $this->assertTrue($result);
        $this->assertEquals("Updated Title", $row['title']);
        $this->assertEquals("Updated Description", $row['description']);
    }

    public function testDelete(): void
    {
        $this->connection->insert('tasks', [
            'title' => 'Delete Me',
            'description' => 'Testing delete method'
        ]);

        $result = $this->taskRepository->delete(1);
        $task = $this->taskRepository->find(1);

        $this->assertTrue($result);
        $this->assertNull($task);
    }
}
