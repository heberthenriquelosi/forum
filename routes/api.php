<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\AnswerController;

// Rotas de Autenticação
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rotas Públicas de Questions (Perguntas)
Route::get('/questions', [QuestionController::class, 'index']);
Route::get('/questions/{question}', [QuestionController::class, 'show']);

// Rotas Públicas de Answers (Respostas de uma Pergunta)
Route::get('/questions/{question}/answers', [AnswerController::class, 'index']);
Route::get('/answers/{answer}', [AnswerController::class, 'show']);

// Rotas Protegidas de Questions (Perguntas)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::put('/questions/{question}', [QuestionController::class, 'update']);
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy']);
});

// Rotas Protegidas de Answers (Respostas de uma Pergunta)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/questions/{question}/answers', [AnswerController::class, 'store']);
    Route::put('/answers/{answer}', [AnswerController::class, 'update']);
    Route::delete('/answers/{answer}', [AnswerController::class, 'destroy']);
});
