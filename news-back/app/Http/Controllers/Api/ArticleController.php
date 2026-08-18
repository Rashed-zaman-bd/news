<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * List articles (admin: all statuses; public: published only via separate route/scope if needed).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Article::with(['category', 'author', 'editor'])
            ->latest('published_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        if ($request->filled('is_breaking')) {
            $query->where('is_breaking', $request->boolean('is_breaking'));
        }

        if ($request->filled('search')) {
            $query->whereFullText(['title', 'content'], $request->search);
        }

        $articles = $query->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => ArticleResource::collection($articles),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

    /**
     * Store a new article.
     */
    public function store(StoreArticleRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['slug'] = $data['slug'] ?? $this->generateUniqueSlug($data['title']);
        $data['user_id'] = $request->user()->id;
        $data['status'] = $data['status'] ?? 'draft';

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        $article = Article::create($data);

        return response()->json([
            'message' => 'আর্টিকেল সফলভাবে তৈরি হয়েছে।',
            'data' => new ArticleResource($article->load(['category', 'author'])),
        ], 201);
    }

    /**
     * Show a single article.
     */
    public function show(Article $article): JsonResponse
    {
        $article->load(['category', 'author', 'editor']);

        return response()->json([
            'data' => new ArticleResource($article),
        ]);
    }

    /**
     * Update an article.
     */
    public function update(UpdateArticleRequest $request, Article $article): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['title']) && empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $article->id);
        }

        // Track who edited it, if the editor differs from the original author
        if ($request->user()->id !== $article->user_id) {
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

        return response()->json([
            'message' => 'আর্টিকেল সফলভাবে আপডেট হয়েছে।',
            'data' => new ArticleResource($article->fresh(['category', 'author', 'editor'])),
        ]);
    }

    /**
     * Soft delete an article.
     */
    public function destroy(Article $article): JsonResponse
    {
        $article->delete();

        return response()->json([
            'message' => 'আর্টিকেল সফলভাবে মুছে ফেলা হয়েছে।',
        ]);
    }

    /**
     * Generate a unique slug from title.
     */
    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
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