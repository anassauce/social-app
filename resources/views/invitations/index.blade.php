<x-app-layout>
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>{{ ucfirst($type) }} Invitations</h2>
                <a href="{{ route('invitations.create') }}" class="btn btn-primary">Send New Invitation</a>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="btn-group" role="group">
                        <a href="{{ route('invitations.index', ['type' => 'received']) }}" class="btn btn-{{ $type === 'received' ? 'secondary active' : 'outline-secondary' }}">Received</a>
                        <a href="{{ route('invitations.index', ['type' => 'sent']) }}" class="btn btn-{{ $type === 'sent' ? 'secondary active' : 'outline-secondary' }}">Sent</a>
                        <a href="{{ route('invitations.pending') }}" class="btn btn-outline-secondary">Pending Only</a>
                        <a href="{{ route('invitations.stats') }}" class="btn btn-outline-secondary">Statistics</a>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info">
                <h5>📨 {{ ucfirst($type) }} Invitations</h5>
                <p class="mb-0">All connection invitations you have {{ $type }}.</p>
            </div>
            
            @if($invitations->count() > 0)
                <div class="row">
                    @foreach($invitations as $invitation)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    @if($type === 'sent')
                                        <h6 class="card-title">To: {{ $invitation->recipient->name }}</h6>
                                        <p class="card-text text-muted">{{ $invitation->recipient->email }}</p>
                                    @else
                                        <h6 class="card-title">From: {{ $invitation->sender->name }}</h6>
                                        <p class="card-text text-muted">{{ $invitation->sender->email }}</p>
                                    @endif
                                    
                                    @if($invitation->message)
                                        <p class="card-text">{{ $invitation->message }}</p>
                                    @endif
                                    
                                    <p class="card-text">
                                        <small class="text-muted">{{ $invitation->created_at->format('M d, Y') }}</small>
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-{{ $invitation->status === 'pending' ? 'warning' : ($invitation->status === 'accepted' ? 'success' : 'danger') }}">
                                            {{ ucfirst($invitation->status) }}
                                        </span>
                                        
                                        @if($invitation->status === 'pending')
                                            @if($type === 'received')
                                                <div class="d-flex gap-1">
                                                    <form method="POST" action="{{ route('invitations.accept', $invitation) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm">Accept</button>
                                                    </form>
                                                    <form method="POST" action="{{ route('invitations.reject', $invitation) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                                    </form>
                                                </div>
                                            @else
                                                <form method="POST" action="{{ route('invitations.destroy', $invitation) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">Cancel</button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $invitations->links() }}
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <div class="text-center py-4">
                            <p class="text-muted">No {{ $type }} invitations found.</p>
                            @if($type === 'sent')
                                <a href="{{ route('invitations.create') }}" class="btn btn-primary">Send Your First Invitation</a>
                            @else
                                <a href="{{ route('connections.suggestions') }}" class="btn btn-primary">Find People to Connect With</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>