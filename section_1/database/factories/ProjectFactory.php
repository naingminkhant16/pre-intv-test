<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'start_date' => $this->faker->dateTimeBetween('now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+3 months'),
            'status' => $this->faker->randomElement(ProjectStatus::values()),
        ];
    }
}
