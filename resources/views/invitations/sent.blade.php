<x-app-layout>
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Sent Invitations</h2>
                <a href="{{ route('invitations.create') }}" class="btn btn-primary">Send New Invitation</a>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="btn-group" role="group">
                        <a href="{{ route('invitations.index') }}" class="btn btn-outline-secondary">All Invitations</a>
                        <a href="{{ route('invitations.pending') }}" class="btn btn-outline-secondary">Pending</a>
                        <a href="{{ route('invitations.sent') }}" class="btn btn-secondary active">Sent</a>
                        <a href="{{ route('invitations.stats') }}" class="btn btn-outline-secondary">Statistics</a>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info">
                <h5>📤 Sent Invitations</h5>
                <p class="mb-0">This page shows all the connection invitations you have sent to other users.</p>
            </div>
            
            @if($sentInvitations->count() > 0)
                <div class="row">
                    @foreach($sentInvitations as $invitation)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $invitation->recipient->name }}</h6>
                                    <p class="card-text text-muted">{{ $invitation->recipient->email }}</p>
                                    @if($invitation->message)
                                        <p class="card-text">{{ $invitation->message }}</p>
                                    @endif
                                    <p class="card-text">
                                        <small class="text-muted">Sent on {{ $invitation->created_at->format('M d, Y') }}</small>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-{{ $invitation->status === 'pending' ? 'warning' : ($invitation->status === 'accepted' ? 'success' : 'danger') }}">
                                            {{ ucfirst($invitation->status) }}
                                        </span>
                                        @if($invitation->status === 'pending')
                                            <form method="POST" action="{{ route('invitations.destroy', $invitation) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this invitation?')">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $sentInvitations->links() }}
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <div class="text-center py-4">
                            <p class="text-muted">You haven't sent any invitations yet.</p>
                            <a href="{{ route('invitations.create') }}" class="btn btn-primary">Send Your First Invitation</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>