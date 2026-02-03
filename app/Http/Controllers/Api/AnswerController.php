<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AnswerController extends Controller
{
    use AuthorizesRequests;

    public function index(Question $question)
    {
        $answers = $question->answers()
            ->with('author')
            ->orderBy('created_at', 'asc')
            ->get();
            
        return response()->json($answers);
    }


    public function store(Request $request, Question $question)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $answer = Answer::create([
            'author_id' => $request->user()->id,
            'question_id' => $question->id,
            'content' => $request->input('content'),
        ]);

        return response()->json($answer->load('author'), 201);
    }


    public function show(Answer $answer)
    {
        return response()->json($answer->load(['author', 'question']));
    }


    public function update(Request $request, Answer $answer)
    {
        $this->authorize('update', $answer);

        $request->validate([
            'content' => 'required|string',
        ]);

        $answer->update([
            'content' => $request->input('content'),
        ]);

        return response()->json($answer->load('author'));
    }


    public function destroy(Request $request, Answer $answer)
    {
        $this->authorize('delete', $answer);

        $answer->delete();
        
        return response()->json(['message' => 'Resposta deletada com sucesso']);
    }
}
