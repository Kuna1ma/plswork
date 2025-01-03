<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\QuestionController;

// Question Form Route
Route::get('/questions', function () {
    return view('questions.form');
})->name('questions.form');

// Question Generation Route
Route::post('/questions/generate', [QuestionController::class, 'generateQuestions'])->name('questions.generate');

// Save Questions Route
Route::post('/save-questions', function (\Illuminate\Http\Request $request) {
    $questions = $request->input('questions');

    // Save questions logic here (e.g., database or file storage)
    foreach ($questions as $index => $questionData) {
        // Example of saving question
        // Question::create($questionData);
    }

    return redirect()->route('questions.form')->with('success', 'Questions saved successfully!');
})->name('save.questions');



Route::post('/save-questions', function (\Illuminate\Http\Request $request) {
    $questions = $request->input('questions');

    // Save or update questions logic
    foreach ($questions as $questionData) {
        // Example: Save question and options to a database
        // Question::updateOrCreate(['id' => $questionData['id']], $questionData);
    }

    return redirect()->route('questions.form')->with('success', 'Questions saved successfully!');
})->name('save.questions');





// Logout Route

Route::post('/logout', function () {
    auth()->logout();
    return redirect('/')->with('message', 'Logged out successfully.');
})->name('logout');

// Dashboard Redirect
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'student') {
        return redirect()->route('student.dashboard');
    } elseif (auth()->user()->role === 'instructor') {
        return redirect()->route('instructor.dashboard');
    } else {
        abort(403, 'Unauthorized');
    }
})->middleware('auth')->name('dashboard');

// Student Dashboard
Route::get('/student-dashboard', [StudentController::class, 'index'])
    ->name('student.dashboard')
    ->middleware('auth');

// Instructor Dashboard
Route::get('/instructor-dashboard', [InstructorController::class, 'index'])
    ->name('instructor.dashboard')
    ->middleware('auth');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Welcome Page
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Debug Route for Environment Variables
Route::get('/debug-env', function () {
    return response()->json(['api_key' => env('OPENAI_API_KEY')]);
});

// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// OpenAI API Testing Route
Route::get('/test-openai', function () {
    $apiKey = env('OPENAI_API_KEY'); // Use API key from .env

    if (!$apiKey) {
        return response()->json(['error' => 'OpenAI API key is not set.'], 500);
    }

    try {
        $client = \OpenAI::client($apiKey);

        $response = $client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => 'Generate 3 easy multiple choice questions about science.'],
            ],
            'max_tokens' => 500,
        ]);

        return response()->json($response['choices'][0]['message']['content']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});





// Fallback Route for 404 Errors
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
