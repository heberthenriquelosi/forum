<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestionController extends Controller
{

    public function index()
    {
        $questions = Question::with('author')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($questions);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $question = Question::create([
            'author_id' => $request->user()->id,
            'title' => $request->title,
            'content' => $request->content,
            'slug' => Str::slug($request->title) . '-' . Str::random(8),
        ]);

        return response()->json($question->load('author'), 201);
    }


    public function show(Question $question)
    {
        return response()->json($question->load('author'));
    }


    public function update(Request $request, Question $question)
    {
        // Verificar se é o autor
        if ($question->author_id !== $request->user()->id) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $question->update([
            'title' => $request->title,
            'content' => $request->content,
            'slug' => Str::slug($request->title) . '-' . Str::random(8),
        ]);

        return response()->json($question->load('author'));
    }


    public function destroy(Request $request, Question $question)
    {
        // Verificar se é o autor
        if ($question->author_id !== $request->user()->id) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        $question->delete();
        
        return response()->json(['message' => 'Pergunta deletada com sucesso']);
    }
}
