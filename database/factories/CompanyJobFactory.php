<?php

namespace Database\Factories;

use App\Models\JobCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyJob>
 */
class CompanyJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_id' => uniqid(),
            'user_id' => User::where('role', 'company')->get()->random()->id,
            'job_title' => fake()->jobTitle(),
            'job_description' => fake()->text(),
            'job_category_id' => JobCategory::all()->random()->category_id,
            'job_location' => fake()->city(),
            'job_city' => fake()->city(),
            'job_state' => fake()->state(),
            'job_country' => fake()->country(),
            'job_zip_code' => fake()->postcode(),
            'job_salary' => fake()->randomFloat(2, 1000, 10000),
        ];
    }
}
