<x-app-layout>
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>My Posts</h2>
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
                                        Created {{ $post->created_at->diffForHumans() }}
                                        @if($post->is_ai_generated)
                                            <span class="badge bg-info ms-1">🤖 AI Generated</span>
                                        @endif
                                    </h6>
                                </div>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('posts.show', $post) }}" class="btn btn-outline-primary btn-sm">View</a>
                                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('posts.destroy', $post) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </div>
                            <p class="card-text">{{ Str::limit($post->content, 200) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <span class="badge bg-secondary">{{ ucfirst($post->visibility) }}</span>
                                    <span class="badge bg-{{ $post->status === 'published' ? 'success' : 'warning' }}">{{ ucfirst($post->status) }}</span>
                                </small>
                                <small class="text-muted">
                                    @if($post->updated_at != $post->created_at)
                                        Last updated {{ $post->updated_at->diffForHumans() }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                {{ $posts->links() }}
            @else
                <div class="text-center py-5">
                    <h4>You haven't created any posts yet</h4>
                    <p class="text-muted">Start sharing your thoughts with the world!</p>
                    <div class="mt-3">
                        <a href="{{ route('posts.create') }}" class="btn btn-primary me-2">Create Your First Post</a>
                        <a href="{{ route('posts.ai.generate') }}" class="btn btn-success">🤖 Generate with AI</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>