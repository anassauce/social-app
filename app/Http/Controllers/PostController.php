<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use App\Services\GeminiAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiAIService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function index()
    {
        $user = Auth::user();
        
        $posts = Post::with('user')
            ->forConnectedUsers($user->id)
            ->latest()
            ->paginate(20);

        return view('posts.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'visibility' => 'in:public,connections,private',
            'status' => 'in:draft,published'
        ]);

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'visibility' => $request->visibility ?? 'connections',
            'status' => $request->status ?? 'draft',
            'is_ai_generated' => false
        ]);

        return redirect()->route('posts.show', $post)->with('success', 'Post created successfully!');
    }

    public function show(Post $post)
    {
        $user = Auth::user();
        
        if (!$this->canViewPost($post, $user)) {
            abort(403, 'Unauthorized to view this post');
        }

        try {
            $post->load(['user', 'comments.user', 'likes.user']);
            $isLiked = $post->isLikedBy(Auth::id());
            $likesCount = $post->likesCount();
        } catch (\Exception $e) {
            // If tables don't exist yet, just load the user relationship
            $post->load(['user']);
            $isLiked = false;
            $likesCount = 0;
        }

        return view('posts.show', compact('post', 'isLiked', 'likesCount'));
    }

    public function edit(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to edit this post');
        }

        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to update this post');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'visibility' => 'in:public,connections,private',
            'status' => 'in:draft,published'
        ]);

        $post->update($request->only(['title', 'content', 'visibility', 'status']));

        return redirect()->route('posts.show', $post)->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to delete this post');
        }

        $post->delete();

        return redirect()->route('posts.my-posts')->with('success', 'Post deleted successfully!');
    }

    public function generateWithAI(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:500',
            'tone' => 'in:professional,casual,motivational,humorous,educational',
            'length' => 'in:short,medium,long'
        ]);

        try {
            $content = $this->geminiService->generatePostContent(
                $request->prompt,
                $request->tone ?? 'professional',
                $request->length ?? 'medium'
            );

            $previewData = [
                'title' => $this->generateTitle($request->prompt),
                'content' => $content,
                'prompt' => $request->prompt,
                'tone' => $request->tone ?? 'professional',
                'length' => $request->length ?? 'medium'
            ];

            return view('posts.ai-preview', compact('previewData'));

        } catch (\Exception $e) {
            Log::error('AI post generation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate AI content. Please try again.');
        }
    }

    public function confirmAIPost(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'prompt' => 'required|string|max:500',
            'tone' => 'required|string',
            'length' => 'required|string',
            'visibility' => 'in:public,connections,private',
            'status' => 'in:draft,published'
        ]);

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'visibility' => $request->visibility ?? 'connections',
            'status' => $request->status ?? 'draft',
            'is_ai_generated' => true,
            'ai_prompt' => $request->prompt
        ]);

        return redirect()->route('posts.show', $post)->with('success', 'AI-generated post created successfully!');
    }

    public function improveWithAI(Request $request, Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to improve this post');
        }

        $request->validate([
            'improvements' => 'array',
            'improvements.*' => 'in:grammar,clarity,engagement,professional,concise'
        ]);

        try {
            $improvedContent = $this->geminiService->improveContent(
                $post->content,
                $request->improvements ?? []
            );

            $post->update([
                'content' => $improvedContent,
                'is_ai_generated' => true,
                'ai_prompt' => 'Content improved with AI: ' . implode(', ', $request->improvements ?? [])
            ]);

            return redirect()->route('posts.show', $post)->with('success', 'Post improved with AI successfully!');

        } catch (\Exception $e) {
            Log::error('AI content improvement failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to improve content with AI. Please try again.');
        }
    }

    public function myPosts()
    {
        $posts = Post::where('user_id', Auth::id())
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('posts.my-posts', compact('posts'));
    }

    private function canViewPost(Post $post, $user)
    {
        if ($post->user_id === $user->id) {
            return true;
        }

        if ($post->visibility === 'private') {
            return false;
        }

        if ($post->visibility === 'public' && $post->status === 'published') {
            return true;
        }

        if ($post->visibility === 'connections' && $post->status === 'published') {
            return $user->isConnectedTo($post->user_id);
        }

        return false;
    }

    public function like(Post $post)
    {
        try {
            $user = Auth::user();
            
            if (!$this->canViewPost($post, $user)) {
                return response()->json(['error' => 'Unauthorized to like this post'], 403);
            }

            $existingLike = Like::where('post_id', $post->id)
                               ->where('user_id', Auth::id())
                               ->first();

            if ($existingLike) {
                $existingLike->delete();
                $liked = false;
            } else {
                Like::create([
                    'post_id' => $post->id,
                    'user_id' => Auth::id()
                ]);
                $liked = true;
            }

            return response()->json([
                'liked' => $liked,
                'likes_count' => $post->likesCount()
            ]);
        } catch (\Exception $e) {
            Log::error('Like error: ' . $e->getMessage());
            
            if (str_contains($e->getMessage(), "doesn't exist")) {
                return response()->json(['error' => 'Database tables not created. Please run: php artisan migrate'], 500);
            }
            
            return response()->json(['error' => 'An error occurred while liking the post'], 500);
        }
    }

    public function comment(Request $request, Post $post)
    {
        try {
            $user = Auth::user();
            
            if (!$this->canViewPost($post, $user)) {
                return response()->json(['error' => 'Unauthorized to comment on this post'], 403);
            }

            $request->validate([
                'content' => 'required|string|max:1000',
                'parent_id' => 'nullable|exists:comments,id'
            ]);

            $comment = Comment::create([
                'post_id' => $post->id,
                'user_id' => Auth::id(),
                'content' => $request->content,
                'parent_id' => $request->parent_id
            ]);

            $comment->load('user');

            return response()->json([
                'success' => true,
                'comment' => $comment
            ]);
        } catch (\Exception $e) {
            Log::error('Comment error: ' . $e->getMessage());
            
            if (str_contains($e->getMessage(), "doesn't exist")) {
                return response()->json(['error' => 'Database tables not created. Please run: php artisan migrate'], 500);
            }
            
            return response()->json(['error' => 'An error occurred while posting the comment'], 500);
        }
    }

    private function generateTitle($prompt)
    {
        $words = explode(' ', $prompt);
        $title = implode(' ', array_slice($words, 0, 6));
        return ucfirst($title) . (count($words) > 6 ? '...' : '');
    }
}