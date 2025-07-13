<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Connections</h5>
                    <a href="{{ route('connections.suggestions') }}" class="btn btn-primary btn-sm">
                        Find New Connections
                    </a>
                </div>
                <div class="card-body">
                    @if($connections->count() > 0)
                        <div class="row">
                            @foreach($connections as $connection)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $connection->connectedUser->name }}</h6>
                                            <p class="card-text text-muted">{{ $connection->connectedUser->email }}</p>
                                            <p class="card-text">
                                                <small class="text-muted">Connected on {{ $connection->connected_at->format('M d, Y') }}</small>
                                            </p>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('connections.mutual', $connection->connectedUser) }}" class="btn btn-outline-primary btn-sm">
                                                    Mutual Connections
                                                </a>
                                                <form method="POST" action="{{ route('connections.destroy', $connection) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to remove this connection?')">
                                                        Remove
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $connections->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">You don't have any connections yet.</p>
                            <a href="{{ route('connections.suggestions') }}" class="btn btn-primary">
                                Find People to Connect With
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>