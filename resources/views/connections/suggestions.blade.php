<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Connection Suggestions</h5>
                    <a href="{{ route('connections.index') }}" class="btn btn-outline-primary btn-sm">
                        My Connections
                    </a>
                </div>
                <div class="card-body">
                    @if($suggestions->count() > 0)
                        <div class="row">
                            @foreach($suggestions as $user)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $user->name }}</h6>
                                            <p class="card-text text-muted">{{ $user->email }}</p>
                                            <p class="card-text">
                                                <small class="text-muted">Member since {{ $user->created_at->format('M Y') }}</small>
                                            </p>
                                            <div class="d-flex gap-2">
                                                <form method="POST" action="{{ route('invitations.store') }}" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="recipient_id" value="{{ $user->id }}">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        Send Invitation
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('connections.block', $user) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to block this user?')">
                                                        Block
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No connection suggestions available at the moment.</p>
                            <a href="{{ route('connections.index') }}" class="btn btn-primary">
                                Back to Connections
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>