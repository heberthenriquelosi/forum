<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnswerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_answers_for_question()
    {
        $user = User::factory()->create();
        $question = Question::factory()->create(['author_id' => $user->id]);
        Answer::factory()->count(2)->create([
            'question_id' => $question->id,
            'author_id' => $user->id
        ]);

        $response = $this->getJson("/api/questions/{$question->id}/answers");

        $response->assertStatus(200)
                ->assertJsonCount(2);
    }

    public function test_authenticated_user_can_create_answer()
    {
        $user = User::factory()->create();
        $question = Question::factory()->create(['author_id' => $user->id]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/questions/{$question->id}/answers", [
            'content' => 'This is my answer',
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'id', 'content', 'author'
                ]);

        $this->assertDatabaseHas('answers', [
            'content' => 'This is my answer',
            'question_id' => $question->id,
            'author_id' => $user->id
        ]);
    }

    public function test_author_can_update_own_answer()
    {
        $user = User::factory()->create();
        $question = Question::factory()->create(['author_id' => $user->id]);
        $answer = Answer::factory()->create([
            'question_id' => $question->id,
            'author_id' => $user->id
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/answers/{$answer->id}", [
            'content' => 'Updated answer content',
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('answers', [
            'id' => $answer->id,
            'content' => 'Updated answer content'
        ]);
    }

    public function test_user_cannot_update_others_answer()
    {
        $author = User::factory()->create();
        $otherUser = User::factory()->create();
        $question = Question::factory()->create(['author_id' => $author->id]);
        $answer = Answer::factory()->create([
            'question_id' => $question->id,
            'author_id' => $author->id
        ]);
        $token = $otherUser->createToken('test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/answers/{$answer->id}", [
            'content' => 'Hacked content',
        ]);

        $response->assertStatus(403);
    }
}