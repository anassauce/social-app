<x-app-layout>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Edit Post</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('posts.update', $post) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input 
                                type="text" 
                                name="title" 
                                id="title" 
                                class="form-control @error('title') is-invalid @enderror" 
                                value="{{ old('title', $post->title) }}"
                                required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea 
                                name="content" 
                                id="content" 
                                rows="6" 
                                class="form-control @error('content') is-invalid @enderror" 
                                required>{{ old('content', $post->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="visibility" class="form-label">Visibility</label>
                                    <select name="visibility" id="visibility" class="form-select @error('visibility') is-invalid @enderror">
                                        <option value="connections" {{ old('visibility', $post->visibility) == 'connections' ? 'selected' : '' }}>Connections Only</option>
                                        <option value="public" {{ old('visibility', $post->visibility) == 'public' ? 'selected' : '' }}>Public</option>
                                        <option value="private" {{ old('visibility', $post->visibility) == 'private' ? 'selected' : '' }}>Private</option>
                                    </select>
                                    @error('visibility')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                        <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        @if($post->is_ai_generated)
                            <div class="alert alert-info">
                                <h6>🤖 AI Generated Content</h6>
                                @if($post->ai_prompt)
                                    <small>Original prompt: {{ $post->ai_prompt }}</small>
                                @endif
                            </div>
                        @endif
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Post</button>
                            <div class="btn-group" role="group">
                                <a href="{{ route('posts.show', $post) }}" class="btn btn-outline-secondary">Cancel</a>
                                <a href="{{ route('posts.my-posts') }}" class="btn btn-outline-primary">My Posts</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>