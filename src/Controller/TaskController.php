<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\Interfaces\TaskRepositoryInterface;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    private TaskRepositoryInterface $taskRepository;
    private ProjectRepository $projectRepository;

    public function __construct(TaskRepositoryInterface $taskRepository, ProjectRepository $projectRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->projectRepository = $projectRepository;
    }

    #[Route('/tasks', name: 'tasks_index', methods: ['GET'])]
    public function index(): Response
    {
        $tasks = $this->taskRepository->findAll();
        return $this->render('tasks/list.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/tasks/create', name: 'tasks_create', methods: ['GET'])]
    public function create(): Response {
        $projects = $this->projectRepository->findAll();
        return $this->render('tasks/create.html.twig', compact(['projects']));
    }

    #[Route('/tasks/create', name: 'tasks_store', methods: ['POST'])]
    #[Route('/projects/{project_id}/tasks/create', name: 'projects_tasks_store', methods: ['POST'])]
    public function store(?int $project_id, Request $request): Response
    {
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $project_id = $project_id ?? $request->request->get('project_id');

        $errors = Task::validation($title, $description);
        if (count($errors) > 0) {
                return $this->render('tasks/create.html.twig', [
                'errors' => $errors,
                'title' => $title,
                'description' => $description,
                'project_id' => $project_id
            ]);
        }

        $this->taskRepository->insert(new Task(null, $title, $description, $project_id));
        return $this->redirectToRoute('tasks_index');
    }

    #[Route('/tasks/{id}/edit', name: 'tasks_edit', methods: ['GET'])]
    public function edit(int $id): Response
    {
        $task = $this->taskRepository->find($id);

        return $this->render('tasks/edit.html.twig',
        ['task' => $task]);
    }

    #[Route('/tasks/{id}/edit', name: 'tasks_update', methods: ['POST'])]
    public function update(Request $request, int $id): Response
    {
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $project_id = $request->request->get('project_id');

        $errors = Task::validation($title, $description);
        $task = new Task(null, $title, $description, $project_id);

        if (count($errors) > 0) {
            $task->id = $id;
            return $this->render('tasks/edit.html.twig', [
                'task' => $task,
                'errors' => $errors,
                'title' => $title,
                'description' => $description
            ]);
        }

        $this->taskRepository->update($id, $task);
        return $this->redirectToRoute('tasks_index');
    }

    #[Route('/tasks/{id}/delete', name: 'tasks_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $this->taskRepository->delete($id);
        return $this->redirectToRoute('tasks_index');
    }
}