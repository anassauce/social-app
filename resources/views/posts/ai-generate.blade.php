<x-app-layout>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">🤖 Generate Post with AI</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('posts.ai.generate.post') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="prompt" class="form-label">Describe what you want to write about:</label>
                            <textarea 
                                name="prompt" 
                                id="prompt" 
                                class="form-control @error('prompt') is-invalid @enderror" 
                                rows="4" 
                                placeholder="e.g., Write a motivational post about overcoming challenges..."
                                required>{{ old('prompt') }}</textarea>
                            @error('prompt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="tone" class="form-label">Tone (optional):</label>
                            <select name="tone" id="tone" class="form-select">
                                <option value="">Select tone...</option>
                                <option value="professional" {{ old('tone') == 'professional' ? 'selected' : '' }}>Professional</option>
                                <option value="casual" {{ old('tone') == 'casual' ? 'selected' : '' }}>Casual</option>
                                <option value="motivational" {{ old('tone') == 'motivational' ? 'selected' : '' }}>Motivational</option>
                                <option value="humorous" {{ old('tone') == 'humorous' ? 'selected' : '' }}>Humorous</option>
                                <option value="educational" {{ old('tone') == 'educational' ? 'selected' : '' }}>Educational</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="length" class="form-label">Length (optional):</label>
                            <select name="length" id="length" class="form-select">
                                <option value="">Select length...</option>
                                <option value="short" {{ old('length') == 'short' ? 'selected' : '' }}>Short (1-2 sentences)</option>
                                <option value="medium" {{ old('length') == 'medium' ? 'selected' : '' }}>Medium (1-2 paragraphs)</option>
                                <option value="long" {{ old('length') == 'long' ? 'selected' : '' }}>Long (3+ paragraphs)</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                🤖 Generate Post
                            </button>
                            <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>