<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProjectController extends AbstractController
{
    private ProjectRepository $projectRepository;
    private TaskRepository $taskRepository;

    public function __construct(ProjectRepository $projectRepository, TaskRepository $taskRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->taskRepository = $taskRepository;
    }

    #[Route('/projects', name: 'projects_index', methods: ['GET'])]
    public function index(): Response
    {
        $projects = $this->projectRepository->findAll();
        return $this->render('projects/list.html.twig', ['projects' => $projects]);
    }

    #[Route('/projects/create', name: 'projects_create', methods: ['GET'])]
    public function create(): Response
    {
        return $this->render('projects/create.html.twig');
    }

    #[Route('/projects/create', name: 'projects_store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $title = (string) $request->request->get('title');
        $description = (string) $request->request->get('description');
        $budget = (int) $request->request->get('budget', 0);

        $errors = Project::validation($title, $description, $budget);
        if (count($errors) > 0) {
            return $this->render('projects/create.html.twig', [
                'errors' => $errors,
                'title' => $title,
                'description' => $description,
                'budget' => $budget
            ]);
        }

        $this->projectRepository->insert(new Project(null, $title, $description, (int) $budget));
        return $this->redirectToRoute('projects_index');
    }

    #[Route('/projects/{id}/edit', name: 'projects_edit', methods: ['GET'])]
    public function edit(int $id): Response
    {
        $project = $this->projectRepository->find($id);
        $tasks = $this->taskRepository->findAllByProject($id);

        return $this->render(
            'projects/edit.html.twig',
            ['project' => $project, 'tasks' => $tasks]
        );
    }

    #[Route('/projects/{id}/edit', name: 'projects_update', methods: ['POST'])]
    public function update(Request $request, int $id): Response
    {
        $title = (string) $request->request->get('title');
        $description = (string) $request->request->get('description');
        $budget = (int) $request->request->get('budget', 0);

        $errors = Project::validation($title, $description, $budget);
        $project = new Project(null, $title, $description, $budget);

        if (count($errors) > 0) {
            $project->id = $id;
            return $this->render('projects/edit.html.twig', [
                'project' => $project,
                'errors' => $errors,
                'title' => $title,
                'description' => $description,
                'budget' => $budget
            ]);
        }

        $this->projectRepository->update($id, new Project(null, $title, $description, $budget));
        return $this->redirectToRoute('projects_index');
    }

    #[Route('/projects/{id}/delete', name: 'projects_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $this->projectRepository->delete($id);
        return $this->redirectToRoute('projects_index');
    }

    #[Route('/projects/{id}/tasks/create', name: 'projects_tasks_create', methods: ['GET'])]
    public function createTask(int $id): Response
    {
        $project = $this->projectRepository->find($id);
        return $this->render('projects/tasks/create.html.twig', ['project' => $project]);
    }
}
