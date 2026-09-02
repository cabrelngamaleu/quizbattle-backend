<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Question;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('questions')->get();

        $totalQuestions = Question::count();

        // Pour la categorie "Mix", le nombre de questions disponibles
        // correspond au total toutes categories confondues.
        $categories = $categories->map(function ($category) use ($totalQuestions) {
            if ($category->slug === 'mix') {
                $category->questions_count = $totalQuestions;
            }
            return $category;
        });

        return response()->json($categories);
    }
}
