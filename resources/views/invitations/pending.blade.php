<x-app-layout>
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Pending Invitations</h2>
                <a href="{{ route('invitations.create') }}" class="btn btn-primary">Send New Invitation</a>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="btn-group" role="group">
                        <a href="{{ route('invitations.index') }}" class="btn btn-outline-secondary">All Invitations</a>
                        <a href="{{ route('invitations.pending') }}" class="btn btn-secondary active">Pending</a>
                        <a href="{{ route('invitations.sent') }}" class="btn btn-outline-secondary">Sent</a>
                        <a href="{{ route('invitations.stats') }}" class="btn btn-outline-secondary">Statistics</a>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-warning">
                <h5>⏳ Pending Invitations</h5>
                <p class="mb-0">These are connection invitations waiting for your response.</p>
            </div>
            
            @if($pendingInvitations->count() > 0)
                <div class="row">
                    @foreach($pendingInvitations as $invitation)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $invitation->sender->name }}</h6>
                                    <p class="card-text text-muted">{{ $invitation->sender->email }}</p>
                                    @if($invitation->message)
                                        <p class="card-text">{{ $invitation->message }}</p>
                                    @endif
                                    <p class="card-text">
                                        <small class="text-muted">Received on {{ $invitation->created_at->format('M d, Y') }}</small>
                                    </p>
                                    <div class="d-flex gap-2">
                                        <form method="POST" action="{{ route('invitations.accept', $invitation) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                Accept
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('invitations.reject', $invitation) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this invitation?')">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $pendingInvitations->links() }}
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <div class="text-center py-4">
                            <p class="text-muted">You don't have any pending invitations.</p>
                            <a href="{{ route('connections.suggestions') }}" class="btn btn-primary">Find People to Connect With</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>