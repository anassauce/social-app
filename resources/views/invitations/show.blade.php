<x-app-layout>
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Invitation Details</h2>
                <a href="{{ route('invitations.pending') }}" class="btn btn-outline-secondary">Back to Invitations</a>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        Connection Invitation
                        <span class="badge bg-{{ $invitation->status === 'pending' ? 'warning' : ($invitation->status === 'accepted' ? 'success' : 'danger') }} ms-2">
                            {{ ucfirst($invitation->status) }}
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>From:</h6>
                            <p>{{ $invitation->sender->name }}<br>
                            <small class="text-muted">{{ $invitation->sender->email }}</small></p>
                        </div>
                        <div class="col-md-6">
                            <h6>To:</h6>
                            <p>{{ $invitation->recipient->name }}<br>
                            <small class="text-muted">{{ $invitation->recipient->email }}</small></p>
                        </div>
                    </div>
                    
                    @if($invitation->message)
                        <div class="mt-3">
                            <h6>Message:</h6>
                            <p class="card-text">{{ $invitation->message }}</p>
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <h6>Sent:</h6>
                        <p>{{ $invitation->created_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                    
                    @if($invitation->status !== 'pending')
                        <div class="mt-3">
                            <h6>{{ ucfirst($invitation->status) }}:</h6>
                            <p>{{ $invitation->updated_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                    @endif
                    
                    @if($invitation->status === 'pending' && $invitation->recipient_id === Auth::id())
                        <div class="mt-4">
                            <h6>Actions:</h6>
                            <div class="d-flex gap-2">
                                <form method="POST" action="{{ route('invitations.accept', $invitation) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        Accept Invitation
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('invitations.reject', $invitation) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this invitation?')">
                                        Reject Invitation
                                    </button>
                                </form>
                            </div>
                        </div>
                    @elseif($invitation->status === 'pending' && $invitation->sender_id === Auth::id())
                        <div class="mt-4">
                            <h6>Actions:</h6>
                            <form method="POST" action="{{ route('invitations.destroy', $invitation) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this invitation?')">
                                    Cancel Invitation
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>