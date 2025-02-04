<?php

namespace App\Repository\Interfaces;

use App\Model\Task;

interface TaskRepositoryInterface
{
    public function findAll(): array;
    public function find(int $id): ?Task;
    public function insert(Task $task): bool;
    public function update(int $id, Task $task): bool;
    public function delete(int $id): bool;
}