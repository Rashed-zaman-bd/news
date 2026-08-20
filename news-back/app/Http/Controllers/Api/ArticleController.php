<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Article::with(['category', 'subCategory', 'author', 'editor'])
            ->latest('published_at');

        if ($request->filled('status')) {
            $statuses = is_array($request->status)
                ? $request->status
                : explode(',', $request->status);

            $query->whereIn('status', $statuses);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        if ($request->filled('author_id')) {
            $query->where('user_id', $request->author_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('published_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('published_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->whereFullText(['title', 'content'], $request->search);
        }

        $articles = $query->paginate($request->integer('per_page', 15));

        return ArticleResource::collection($articles);
    }

    public function store(StoreArticleRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['slug'] = !empty($data['slug']) ? $data['slug'] : $this->generateUniqueSlug($data['title']);
        $data['user_id'] = $request->user()->id;
        $data['status'] = $data['status'] ?? 'draft';

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        $article = Article::create($data);

        return (new ArticleResource($article->load(['category', 'author'])))
            ->additional(['message' => 'আর্টিকেল সফলভাবে তৈরি হয়েছে।'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Article $article): ArticleResource
    {
        $article->load(['category', 'author', 'editor']);

        return new ArticleResource($article);
    }

    public function update(UpdateArticleRequest $request, Article $article): ArticleResource
    {
        $data = $request->validated();

        if (array_key_exists('title', $data) && empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $article->id);
        }

        if ($request->user() && $request->user()->id !== $article->user_id) {
            $data['editor_id'] = $request->user()->id;
        }

        if (($data['status'] ?? $article->status) === 'published'
            && !$article->published_at
            && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        $article->update($data);

        return (new ArticleResource($article->fresh(['category', 'author', 'editor'])))
            ->additional(['message' => 'আর্টিকেল সফলভাবে আপডেট হয়েছে।']);
    }

    public function destroy(Article $article): JsonResponse
    {
        $article->delete();

        return response()->json([
            'message' => 'আর্টিকেল সফলভাবে মুছে ফেলা হয়েছে।',
        ], 200);
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        // 'language: null' allows UTF-8 multibyte characters (like Bangla) to stay intact
        $base = Str::slug($title, '-', null);
        
        // Fallback if title contains only symbols that result in an empty string
        if (empty($base)) {
            $base = 'article-' . Str::random(6);
        }

        $slug = $base;
        $counter = 1;

        while (Article::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}