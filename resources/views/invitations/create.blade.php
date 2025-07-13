<x-app-layout>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Send Connection Invitation</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('invitations.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="recipient_id" class="form-label">Select User</label>
                            <select 
                                name="recipient_id" 
                                id="recipient_id" 
                                class="form-select @error('recipient_id') is-invalid @enderror"
                                required>
                                <option value="">Choose a user...</option>
                                {{-- This would typically be populated with available users --}}
                                {{-- You may want to add a controller method to fetch available users --}}
                            </select>
                            @error('recipient_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select a user to send a connection invitation to.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message (Optional)</label>
                            <textarea 
                                name="message" 
                                id="message" 
                                rows="3" 
                                class="form-control @error('message') is-invalid @enderror" 
                                placeholder="Add a personal message to your invitation...">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maximum 500 characters</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Send Invitation</button>
                            <a href="{{ route('invitations.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-4">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Need help finding users?</h6>
                        <p class="card-text text-muted">
                            You can find users to connect with by browsing the community or through mutual connections.
                        </p>
                        <a href="{{ route('connections.suggestions') }}" class="btn btn-outline-primary btn-sm">View Suggestions</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>