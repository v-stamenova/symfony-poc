<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\Interfaces\TaskRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskController extends AbstractController
{
    private TaskRepositoryInterface $repository;

    public function __construct(TaskRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[Route('/tasks', name: 'tasks_index', methods: ['GET'])]
    public function list(): Response
    {
        $tasks = $this->repository->findAll();
        return $this->render('tasks/list.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/tasks/create', name: 'tasks_create', methods: ['GET'])]
    public function create(): Response {
        return $this->render('tasks/create.html.twig');
    }

    #[Route('/tasks/create', name: 'tasks_store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $title = (string) $request->request->get('title');
        $description = (string) $request->request->get('description');

        $errors = Task::validation($title, $description);
        if (count($errors) > 0) {
            return $this->render('tasks/create.html.twig', [
                'errors' => $errors,
                'title' => $title,
                'description' => $description
            ]);
        }

        $this->repository->insert(new Task(null, $title, $description));
        return $this->redirectToRoute('tasks_index');
    }

    #[Route('/tasks/{id}/edit', name: 'tasks_edit', methods: ['GET'])]
    public function edit(int $id): Response {
        $task = $this->repository->find($id);

        return $this->render('tasks/edit.html.twig',
        ['task' => $task]);
    }

    #[Route('/tasks/{id}/edit', name: 'tasks_update', methods: ['POST'])]
    public function update(Request $request, int $id): Response
    {
        $title = (string) $request->request->get('title');
        $description = (string) $request->request->get('description');

        $errors = Task::validation($title, $description);
        if (count($errors) > 0) {
            $task = new Task(null, $title, $description);
            $task->id = $id;
            return $this->render('tasks/edit.html.twig', [
                'task' => $task,
                'errors' => $errors,
                'title' => $title,
                'description' => $description
            ]);
        }

        $this->repository->update($id, new Task(null, $title, $description));
        return $this->redirectToRoute('tasks_index');
    }

    #[Route('/tasks/{id}/delete', name: 'tasks_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $this->repository->delete($id);
        return $this->redirectToRoute('tasks_index');
    }

}