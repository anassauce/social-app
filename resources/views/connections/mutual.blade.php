<x-app-layout>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mutual Connections with {{ $user->name }}</h5>
                    <a href="{{ route('connections.index') }}" class="btn btn-outline-primary btn-sm">
                        Back to Connections
                    </a>
                </div>
                <div class="card-body">
                    @if($mutualConnections->count() > 0)
                        <div class="row">
                            @foreach($mutualConnections as $connection)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $connection->name }}</h6>
                                            <p class="card-text text-muted">{{ $connection->email }}</p>
                                            <p class="card-text">
                                                <small class="text-muted">Member since {{ $connection->created_at->format('M Y') }}</small>
                                            </p>
                                            <div class="d-flex gap-2">
                                                <form method="GET" action="{{ route('connections.mutual', $connection) }}" class="d-inline">
                                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                                        View Mutual
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
                            <p class="text-muted">You don't have any mutual connections with {{ $user->name }}.</p>
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