<?php

namespace App\Services;

use App\Dtos\ProjectDto;
use App\Models\Project;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

readonly class ProjectService
{
    public function __construct(private Project $model)
    {
    }

    public function getAll(): LengthAwarePaginator
    {
        return $this->model->query()->latest()->paginate(10);
    }

    public function create(ProjectDto $dto): Project
    {
        $project = $this->model->create([
                'name' => $dto->getName(),
                'description' => $dto->getDescription(),
                'start_date' => $dto->getStartDate(),
                'end_date' => $dto->getEndDate(),
                'status' => $dto->getStatus(),
            ]
        );

        Log::info("Project created: {$project->id}");

        return $project;
    }

    public function update(Project $project, ProjectDto $dto): Project
    {
        $project->update([
            'name' => $dto->getName(),
            'description' => $dto->getDescription(),
            'start_date' => $dto->getStartDate(),
            'end_date' => $dto->getEndDate(),
            'status' => $dto->getStatus(),
        ]);

        Log::info("Project updated: {$project->id}");

        return $project;
    }

    public function delete(Project $project, bool $hardDelete = false): void
    {
        if ($hardDelete) {
            $project->forceDelete();
            return;
        }
        
        $project->delete();
    }
}
