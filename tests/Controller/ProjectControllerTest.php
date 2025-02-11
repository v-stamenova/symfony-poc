<?php

namespace App\Tests\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProjectControllerTest extends WebTestCase
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
        $this->projectRepositoryMock->method('findAll')->willReturn([
            new Project(1, 'Test Project 1', 'Description 1', 100),
            new Project(2, 'Test Project 2', 'Description 2', 100)
        ]);

        $this->client->request('GET', '/projects');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame('html');

        $responseData = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('Test Project 1', $responseData);
        $this->assertStringContainsString('Test Project 2', $responseData);
    }

    public function testCreate(): void
    {
        $this->client->request('GET', '/projects/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame('html');
    }

    public function testStore(): void
    {
        $this->projectRepositoryMock
            ->method('insert')
            ->with($this->callback(function ($project) {
                return $project instanceof Project &&
                    $project->title === 'Test Project 1' &&
                    $project->description === 'Description 1' &&
                    $project->budget == 100;
            }))
            ->willReturn(true);

        $this->client->request('POST', '/projects/create', [
            'title' => 'Test Project 1',
            'description' => 'Description 1',
            'budget' => 100
        ]);

        $this->assertResponseRedirects('/projects');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testStoreWithErrors(): void
    {
        $this->client->request('POST', '/projects/create', [
            'id' => '1',
            'title' => 'T',
            'description' => 'Description 1',
            'budget' => 5,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame('html');
        $responseData = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('Title must be between 3 and 255 characters.', $responseData);
    }

    public function testEdit(): void
    {
        $this->projectRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(new Project(1, 'Test Project 1', 'Description 1', 100));

        $this->taskRepositoryMock
            ->method('findAllByProject')
            ->with(1)
            ->willReturn([]);

        $this->client->request('GET', '/projects/1/edit');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame('html');

        $responseData = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('Test Project 1', $responseData);
    }

    public function testUpdate(): void
    {
        $this->projectRepositoryMock
            ->method('update')
            ->with(1, $this->callback(function ($project) {
                return $project instanceof Project &&
                    $project->title === 'Test Project 1' &&
                    $project->description === 'Description 1' &&
                    $project->budget == 100;
            }))
            ->willReturn(true);


        $this->client->request('POST', '/projects/1/edit', [
            'id' => '1',
            'title' => 'Test Project 1',
            'description' => 'Description 1',
            'budget' => 100
        ]);

        $this->assertResponseRedirects('/projects');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testUpdateWithErrors(): void
    {
        $this->client->request('POST', '/projects/1/edit', [
            'id' => '1',
            'title' => 'T',
            'description' => 'Description 1',
            'budget' => 5,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame('html');
        $responseData = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('Title must be between 3 and 255 characters.', $responseData);
    }

    public function testDelete(): void
    {
        $this->projectRepositoryMock
            ->method('delete')
            ->with(1)
            ->willReturn(true);

        $this->client->request('POST', '/projects/1/delete');

        $this->assertResponseRedirects('/projects');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
