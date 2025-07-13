<x-app-layout>
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Invitation Statistics</h2>
                <a href="{{ route('invitations.create') }}" class="btn btn-primary">Send New Invitation</a>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="btn-group" role="group">
                        <a href="{{ route('invitations.index') }}" class="btn btn-outline-secondary">All Invitations</a>
                        <a href="{{ route('invitations.pending') }}" class="btn btn-outline-secondary">Pending</a>
                        <a href="{{ route('invitations.sent') }}" class="btn btn-outline-secondary">Sent</a>
                        <a href="{{ route('invitations.stats') }}" class="btn btn-secondary active">Statistics</a>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-primary">
                <h5>📊 Invitation Statistics</h5>
                <p class="mb-0">Overview of your invitation activity.</p>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">📤 Sent Invitations</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center">
                                        <h3 class="text-primary">{{ $stats['sent']['total'] }}</h3>
                                        <p class="text-muted mb-0">Total Sent</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h3 class="text-warning">{{ $stats['sent']['pending'] }}</h3>
                                        <p class="text-muted mb-0">Pending</p>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center">
                                        <h3 class="text-success">{{ $stats['sent']['accepted'] }}</h3>
                                        <p class="text-muted mb-0">Accepted</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h3 class="text-danger">{{ $stats['sent']['rejected'] }}</h3>
                                        <p class="text-muted mb-0">Rejected</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('invitations.sent') }}" class="btn btn-outline-primary btn-sm">View Sent Invitations</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">📥 Received Invitations</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center">
                                        <h3 class="text-primary">{{ $stats['received']['total'] }}</h3>
                                        <p class="text-muted mb-0">Total Received</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h3 class="text-warning">{{ $stats['received']['pending'] }}</h3>
                                        <p class="text-muted mb-0">Pending</p>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center">
                                        <h3 class="text-success">{{ $stats['received']['accepted'] }}</h3>
                                        <p class="text-muted mb-0">Accepted</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h3 class="text-danger">{{ $stats['received']['rejected'] }}</h3>
                                        <p class="text-muted mb-0">Rejected</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('invitations.pending') }}" class="btn btn-outline-primary btn-sm">View Pending Invitations</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($stats['sent']['total'] > 0 || $stats['received']['total'] > 0)
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <h4 class="text-info">{{ $stats['sent']['total'] + $stats['received']['total'] }}</h4>
                                        <p class="text-muted">Total Invitations</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="text-warning">{{ $stats['sent']['pending'] + $stats['received']['pending'] }}</h4>
                                        <p class="text-muted">Awaiting Response</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="text-success">{{ $stats['sent']['accepted'] + $stats['received']['accepted'] }}</h4>
                                        <p class="text-muted">Successful Connections</p>
                                    </div>
                                    <div class="col-md-3">
                                        @php
                                            $total = $stats['sent']['total'] + $stats['received']['total'];
                                            $accepted = $stats['sent']['accepted'] + $stats['received']['accepted'];
                                            $successRate = $total > 0 ? round(($accepted / $total) * 100) : 0;
                                        @endphp
                                        <h4 class="text-primary">{{ $successRate }}%</h4>
                                        <p class="text-muted">Success Rate</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <div class="text-center py-4">
                            <p class="text-muted">No invitation activity yet.</p>
                            <a href="{{ route('invitations.create') }}" class="btn btn-primary">Send Your First Invitation</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>