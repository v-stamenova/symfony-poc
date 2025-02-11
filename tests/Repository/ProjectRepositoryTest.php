<?php

namespace App\Tests\Repository;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

class ProjectRepositoryTest extends TestCase
{
    private Connection $connection;
    private ProjectRepository $projectRepository;

    protected function setUp(): void
    {
        $this->connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]);

        $this->connection->executeStatement("
            CREATE TABLE IF NOT EXISTS projects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT,
                budget INTEGER
            )
        ");

        $this->projectRepository = new ProjectRepository($this->connection);
    }

    public function testFindAll(): void
    {
        $this->connection->insert(
            'projects',
            ['title' => 'Project 1', 'description' => 'Description 1', 'budget' => 100]
        );
        $this->connection->insert(
            'projects',
            ['title' => 'Project 2', 'description' => 'Description 2', 'budget' => 100]
        );

        $projects = $this->projectRepository->findAll();

        $this->assertCount(2, $projects);
        $this->assertEquals('Project 2', $projects[0]->title);
        $this->assertEquals('Project 1', $projects[1]->title);
    }

    public function testFind(): void
    {
        $this->connection->insert('projects', [
            'title' => 'Find Me',
            'description' => 'Testing find method',
            'budget' => 100,
        ]);

        $project = $this->projectRepository->find(1);

        $this->assertNotNull($project);
        $this->assertEquals('Find Me', $project->title);
        $this->assertEquals('Testing find method', $project->description);
        $this->assertEquals(100, $project->budget);
    }

    public function testInsert(): void
    {
        $project = new Project(1, "Test Project", "This is a test project", 100);
        $result = $this->projectRepository->insert($project);

        $stmt = $this->connection->executeQuery("SELECT * FROM projects WHERE id = ?", [1]);
        $row = $stmt->fetchAssociative();

        $this->assertTrue($result);
        $this->assertNotFalse($row);
        $this->assertEquals("Test Project", $row['title']);
        $this->assertEquals("This is a test project", $row['description']);
        $this->assertEquals(100, $row['budget']);
    }

    public function testUpdate(): void
    {
        $this->connection->insert(
            'projects',
            ['title' => 'Old Title', 'description' => 'Old Description', 'budget' => 50]
        );

        $project = new Project(1, "Updated Title", "Updated Description", 100);
        $result = $this->projectRepository->update(1, $project);

        $stmt = $this->connection->executeQuery("SELECT * FROM projects WHERE id = ?", [1]);
        $row = $stmt->fetchAssociative();

        $this->assertTrue($result);
        $this->assertEquals("Updated Title", $row['title']);
        $this->assertEquals("Updated Description", $row['description']);
        $this->assertEquals(100, $row['budget']);
    }

    public function testDelete(): void
    {
        $this->connection->insert('projects', [
            'title' => 'Delete Me',
            'description' => 'Testing delete method'
        ]);

        $result = $this->projectRepository->delete(1);
        $task = $this->projectRepository->find(1);

        $this->assertTrue($result);
        $this->assertNull($task);
    }
}
