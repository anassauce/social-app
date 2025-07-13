<x-app-layout>
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Posts Feed</h2>
                <div>
                    <a href="{{ route('posts.create') }}" class="btn btn-primary">Create Post</a>
                    <a href="{{ route('posts.ai.generate') }}" class="btn btn-success">🤖 AI Generate</a>
                </div>
            </div>
            
            @if($posts->count() > 0)
                @foreach($posts as $post)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="card-title">{{ $post->title }}</h5>
                                    <h6 class="card-subtitle mb-2 text-muted">
                                        By {{ $post->user->name }} • {{ $post->created_at->diffForHumans() }}
                                        @if($post->is_ai_generated)
                                            <span class="badge bg-info ms-1">🤖 AI Generated</span>
                                        @endif
                                    </h6>
                                </div>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('posts.show', $post) }}" class="btn btn-outline-primary btn-sm">View</a>
                                    @if($post->user_id === Auth::id())
                                        <a href="{{ route('posts.edit', $post) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                                    @endif
                                </div>
                            </div>
                            <p class="card-text">{{ Str::limit($post->content, 200) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <span class="badge bg-secondary">{{ ucfirst($post->visibility) }}</span>
                                    <span class="badge bg-{{ $post->status === 'published' ? 'success' : 'warning' }}">{{ ucfirst($post->status) }}</span>
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                {{ $posts->links() }}
            @else
                <div class="text-center py-5">
                    <h4>No posts to display</h4>
                    <p class="text-muted">Be the first to create a post!</p>
                    <a href="{{ route('posts.create') }}" class="btn btn-primary">Create Your First Post</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>