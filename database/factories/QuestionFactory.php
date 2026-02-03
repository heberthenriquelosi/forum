<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class QuestionFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence();
        
        return [
            'author_id' => User::factory(),
            'title' => $title,
            'content' => fake()->paragraphs(3, true),
            'slug' => Str::slug($title) . '-' . Str::random(8),
        ];
    }
}