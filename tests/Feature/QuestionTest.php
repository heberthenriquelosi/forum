<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_questions()
    {
        $user = User::factory()->create();
        Question::factory()->count(3)->create(['author_id' => $user->id]);

        $response = $this->getJson('/api/questions');

        $response->assertStatus(200)
                ->assertJsonCount(3);
    }

    public function test_authenticated_user_can_create_question()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/questions', [
            'title' => 'Test Question',
            'content' => 'This is a test question content',
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'id', 'title', 'content', 'slug', 'author'
                ]);

        $this->assertDatabaseHas('questions', [
            'title' => 'Test Question',
            'author_id' => $user->id
        ]);
    }

    public function test_unauthenticated_user_cannot_create_question()
    {
        $response = $this->postJson('/api/questions', [
            'title' => 'Test Question',
            'content' => 'This is a test question content',
        ]);

        $response->assertStatus(401);
    }

    public function test_author_can_update_own_question()
    {
        $user = User::factory()->create();
        $question = Question::factory()->create(['author_id' => $user->id]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/questions/{$question->id}", [
            'title' => 'Updated Title',
            'content' => 'Updated content',
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('questions', [
            'id' => $question->id,
            'title' => 'Updated Title'
        ]);
    }

    public function test_user_cannot_update_others_question()
    {
        $author = User::factory()->create();
        $otherUser = User::factory()->create();
        $question = Question::factory()->create(['author_id' => $author->id]);
        $token = $otherUser->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/questions/{$question->id}", [
            'title' => 'Hacked Title',
            'content' => 'Hacked content',
        ]);

        $response->assertStatus(403);
    }
}