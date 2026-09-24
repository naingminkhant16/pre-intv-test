<?php

namespace App\Http\Controllers;

use App\Dtos\ProjectDto;
use App\Enums\ProjectStatus;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProjectController extends Controller
{
    public function __construct(private readonly ProjectService $projectService)
    {
    }

    public function index(): ResourceCollection
    {
        return ProjectResource::collection($this->projectService->getAll());
    }

    public function store(ProjectRequest $request): JsonResponse
    {
        try {
            $project = $this->projectService->create(new ProjectDto(
                $request->name,
                $request->description,
                $request->start_date,
                $request->end_date,
                ProjectStatus::from($request->status)
            ));

            return response()->json(['data' => new ProjectResource($project)],201);
        } catch (Throwable $exception) {
            Log::error($exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function show(Project $project): JsonResource
    {
        return new ProjectResource($project);
    }

    public function update(Project $project, ProjectRequest $request): JsonResponse
    {
        try {
            $project = $this->projectService->update($project, new ProjectDto(
                $request->name,
                $request->description,
                $request->start_date,
                $request->end_date,
                ProjectStatus::from($request->status)
            ));

            return response()->json(['data' => new ProjectResource($project)]);
        } catch (Throwable $exception) {
            Log::error($exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function destroy(Project $project)
    {
        $this->projectService->delete($project);

        return response()->json(null, 204);
    }
}
