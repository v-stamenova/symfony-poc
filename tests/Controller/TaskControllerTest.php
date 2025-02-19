<?php

namespace App\Tests\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private $client;
    private TaskRepository $taskRepositoryMock;
    private ProjectRepository $projectRepositoryMock;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->taskRepositoryMock = $this->createMock(TaskRepository::class);
        $this->projectRepositoryMock = $this->createMock(ProjectRepository::class);

        static::getContainer()->set(TaskRepository::class, $this->taskRepositoryMock);
        static::getContainer()->set(ProjectRepository::class, $this->projectRepositoryMock);
    }

    public function testIndex(): void
    {
        $this->taskRepositoryMock->method('findAll')->willReturn([
            new Task(1, 'Test Task 1', 'Description 1', null),
            new Task(2, 'Test Task 2', 'Description 2', null)
        ]);

        $this->client->request('GET', '/tasks');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame('html');

        $responseData = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('Test Task 1', $responseData);
        $this->assertStringContainsString('Test Task 2', $responseData);
    }

    public function testCreate(): void
    {
        $this->projectRepositoryMock
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([
                new Project(1, 'Test Project 1', 'Description 1', 100),
                new Project(2, 'Test Project 2', 'Description 2', 100)
            ]);

        $this->client->request('GET', '/tasks/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame('html');
    }

    public function testStore(): void
    {
        $this->taskRepositoryMock
            ->method('insert')
            ->with($this->callback(function ($task) {
                return $task instanceof Task &&
                    $task->title === 'Test Task 1' &&
                    $task->description === 'Description 1';
            }))
            ->willReturn(true);

        $this->client->request('POST', '/tasks/create', [
            'title' => 'Test Task 1',
            'description' => 'Description 1',
        ]);

        $this->assertResponseRedirects('/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testStoreWithErrors(): void
    {
        $this->client->request('POST', '/tasks/create', [
            'id' => '1',
            'title' => 'T',
            'description' => 'Description 1',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame('html');
        $responseData = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('Title must be between 3 and 255 characters.', $responseData);
    }


    public function testEdit(): void
    {
        $this->taskRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(new Task(1, 'Test Task 1', 'Description 1', null));

        $this->client->request('GET', '/tasks/1/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame('html');

        $responseData = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('Test Task 1', $responseData);
    }

    public function testUpdate(): void
    {
        $this->taskRepositoryMock
            ->method('update')
            ->with(1, $this->callback(function ($task) {
                return $task instanceof Task &&
                    $task->title === 'Test Task 1' &&
                    $task->description === 'Description 1';
            }))
            ->willReturn(true);


        $this->client->request('POST', '/tasks/1/edit', [
            'id' => '1',
            'title' => 'Test Task 1',
            'description' => 'Description 1',
        ]);

        $this->assertResponseRedirects('/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testUpdateWithErrors(): void
    {
        $this->client->request('POST', '/tasks/1/edit', [
            'id' => '1',
            'title' => 'T',
            'description' => 'Description 1',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame('html');
        $responseData = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('Title must be between 3 and 255 characters.', $responseData);
    }

    public function testDelete(): void
    {
        $this->taskRepositoryMock
            ->method('delete')
            ->with(1)
            ->willReturn(true);

        $this->client->request('POST', '/tasks/1/delete', [
            'id' => '1',
        ]);

        $this->assertResponseRedirects('/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
