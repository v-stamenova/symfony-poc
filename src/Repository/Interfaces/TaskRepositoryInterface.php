<?php

namespace App\Repository\Interfaces;

use App\Entity\Task;

interface TaskRepositoryInterface
{
    /**
     * @return Task[]
     */
    public function findAll(): array;
    public function find(int $id): ?Task;
    public function insert(Task $task): bool;
    public function update(int $id, Task $task): bool;
    public function delete(int $id): bool;
}