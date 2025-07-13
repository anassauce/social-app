<x-app-layout>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">🤖 AI Generated Post Preview</h4>
                    <small class="text-muted">Review your AI-generated content before posting</small>
                </div>
                <div class="card-body">
                    <!-- Preview Section -->
                    <div class="alert alert-info mb-4">
                        <h6>📝 Generation Details:</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Prompt:</strong> {{ $previewData['prompt'] }}
                            </div>
                            <div class="col-md-4">
                                <strong>Tone:</strong> {{ ucfirst($previewData['tone']) }}
                            </div>
                            <div class="col-md-4">
                                <strong>Length:</strong> {{ ucfirst($previewData['length']) }}
                            </div>
                        </div>
                    </div>

                    <form id="confirmForm" method="POST" action="{{ route('posts.ai.confirm') }}">
                        @csrf
                        
                        <!-- Hidden fields to preserve AI generation data -->
                        <input type="hidden" name="prompt" value="{{ $previewData['prompt'] }}">
                        <input type="hidden" name="tone" value="{{ $previewData['tone'] }}">
                        <input type="hidden" name="length" value="{{ $previewData['length'] }}">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input 
                                type="text" 
                                name="title" 
                                id="title" 
                                class="form-control @error('title') is-invalid @enderror" 
                                value="{{ old('title', $previewData['title']) }}"
                                required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">You can edit the title before posting</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea 
                                name="content" 
                                id="content" 
                                rows="8" 
                                class="form-control @error('content') is-invalid @enderror" 
                                required>{{ old('content', $previewData['content']) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">You can edit the content before posting</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="visibility" class="form-label">Visibility</label>
                                    <select name="visibility" id="visibility" class="form-select @error('visibility') is-invalid @enderror">
                                        <option value="connections" {{ old('visibility') == 'connections' ? 'selected' : '' }}>Connections Only</option>
                                        <option value="public" {{ old('visibility') == 'public' ? 'selected' : '' }}>Public</option>
                                        <option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>Private</option>
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
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <div class="btn-group" role="group">
                                <button type="submit" class="btn btn-success btn-lg">
                                    ✅ Confirm & Post
                                </button>
                                <button type="button" class="btn btn-warning btn-lg" onclick="regenerateContent()">
                                    🔄 Regenerate
                                </button>
                                <a href="{{ route('posts.ai.generate') }}" class="btn btn-outline-secondary btn-lg">
                                    ❌ Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Live Preview Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">👀 Live Preview</h6>
                    <small class="text-muted">How your post will appear to others</small>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="card-title" id="preview-title">{{ $previewData['title'] }}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">
                                By {{ Auth::user()->name }} • Just now
                                <span class="badge bg-info ms-1">🤖 AI Generated</span>
                            </h6>
                        </div>
                    </div>
                    <p class="card-text" id="preview-content" style="white-space: pre-wrap;">{{ $previewData['content'] }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <span class="badge bg-secondary" id="preview-visibility">Connections Only</span>
                            <span class="badge bg-warning" id="preview-status">Draft</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update live preview when form fields change
        document.getElementById('title').addEventListener('input', function() {
            document.getElementById('preview-title').textContent = this.value;
        });
        
        document.getElementById('content').addEventListener('input', function() {
            document.getElementById('preview-content').textContent = this.value;
        });
        
        document.getElementById('visibility').addEventListener('change', function() {
            document.getElementById('preview-visibility').textContent = this.options[this.selectedIndex].text;
        });
        
        document.getElementById('status').addEventListener('change', function() {
            const statusBadge = document.getElementById('preview-status');
            statusBadge.textContent = this.options[this.selectedIndex].text;
            statusBadge.className = this.value === 'published' ? 'badge bg-success' : 'badge bg-warning';
        });
        
        function regenerateContent() {
            if (confirm('Are you sure you want to regenerate the content? Current changes will be lost.')) {
                // Go back to AI generate form with the same parameters
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('posts.ai.generate.post') }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                const prompt = document.createElement('input');
                prompt.type = 'hidden';
                prompt.name = 'prompt';
                prompt.value = '{{ $previewData['prompt'] }}';
                form.appendChild(prompt);
                
                const tone = document.createElement('input');
                tone.type = 'hidden';
                tone.name = 'tone';
                tone.value = '{{ $previewData['tone'] }}';
                form.appendChild(tone);
                
                const length = document.createElement('input');
                length.type = 'hidden';
                length.name = 'length';
                length.value = '{{ $previewData['length'] }}';
                form.appendChild(length);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-app-layout>