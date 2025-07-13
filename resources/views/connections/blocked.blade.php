<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Blocked Users</h5>
                    <a href="{{ route('connections.index') }}" class="btn btn-outline-primary btn-sm">
                        Back to Connections
                    </a>
                </div>
                <div class="card-body">
                    @if($blockedUsers->count() > 0)
                        <div class="row">
                            @foreach($blockedUsers as $blockedConnection)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $blockedConnection->connectedUser->name }}</h6>
                                            <p class="card-text text-muted">{{ $blockedConnection->connectedUser->email }}</p>
                                            <p class="card-text">
                                                <small class="text-muted">Blocked on {{ $blockedConnection->connected_at->format('M d, Y') }}</small>
                                            </p>
                                            <form method="POST" action="{{ route('connections.unblock', $blockedConnection->connectedUser) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to unblock this user?')">
                                                    Unblock
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $blockedUsers->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">You haven't blocked any users.</p>
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