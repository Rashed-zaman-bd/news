<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleImageController extends Controller
{
    public function update(Request $request, Article $article, ArticleImage $articleImage): JsonResponse
{
    abort_if($articleImage->article_id !== $article->id, 404);

    $data = $request->validate([
        'caption' => 'nullable|string|max:255',
    ]);

    $articleImage->update($data);

    return response()->json(['message' => 'ক্যাপশন আপডেট হয়েছে।', 'data' => $articleImage]);
}

public function destroy(Article $article, ArticleImage $articleImage): JsonResponse
{
    abort_if($articleImage->article_id !== $article->id, 404);

    Storage::disk('public')->delete($articleImage->image_path);
    $articleImage->delete();

    return response()->json(['message' => 'ছবি মুছে ফেলা হয়েছে।']);
}
}