<?php

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists projects', function () {
    Project::factory()->count(3)->create();

    $response = $this->getJson('/api/projects');

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'start_date',
                    'end_date',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta',
        ]);
});

it('creates a project', function () {
    $payload = [
        'name' => 'Website redesign',
        'description' => 'Redesign the marketing site and improve conversion flow.',
        'start_date' => '2026-09-24',
        'end_date' => '2026-10-30',
        'status' => ProjectStatus::IN_PROGRESS->value,
    ];

    $response = $this->postJson('/api/projects', $payload);

    $response
        ->assertStatus(201)
        ->assertJsonPath('data.name', 'Website redesign')
        ->assertJsonPath('data.status', ProjectStatus::IN_PROGRESS->value);

    $this->assertDatabaseHas('projects', [
        'name' => 'Website redesign',
        'status' => ProjectStatus::IN_PROGRESS->value,
    ]);
});

it('shows a single project', function () {
    $project = Project::factory()->create();

    $response = $this->getJson('/api/projects/' . $project->id);

    $response
        ->assertOk()
        ->assertJsonPath('data.id', $project->id)
        ->assertJsonPath('data.name', $project->name);
});

it('updates a project', function () {
    $project = Project::factory()->create();

    $payload = [
        'name' => 'Updated project name',
        'description' => 'Updated description for the project.',
        'start_date' => '2026-09-25',
        'end_date' => '2026-11-10',
        'status' => ProjectStatus::COMPLETED->value,
    ];

    $response = $this->putJson('/api/projects/' . $project->id, $payload);

    $response
        ->assertOk()
        ->assertJsonPath('data.name', 'Updated project name')
        ->assertJsonPath('data.status', ProjectStatus::COMPLETED->value);

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Updated project name',
        'status' => ProjectStatus::COMPLETED->value,
    ]);
});

it('deletes a project', function () {
    $project = Project::factory()->create();

    $response = $this->deleteJson('/api/projects/' . $project->id);

    $response->assertNoContent();

    $this->assertSoftDeleted('projects', ['id' => $project->id]);
});

it('validation checks', function () {
    $response = $this->postJson('/api/projects', [
        'name' => 'Bad',
        'description' => 'Bad',
        'start_date' => '2026-10-31',
        'end_date' => '2026-10-01',
        'status' => 'unknown',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'description', 'end_date', 'status']);
});
