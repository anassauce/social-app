<x-app-layout>
    <x-slot name="title">Dashboard - {{ config('app.name') }}</x-slot>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2">Welcome back, {{ Auth::user()->name }}! 👋</h1>
                <div class="text-muted">{{ now()->format('l, F j, Y') }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Quick Stats -->
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-primary">📝 My Posts</h5>
                    <h3 class="card-text">{{ Auth::user()->posts()->count() }}</h3>
                    <a href="{{ route('posts.my-posts') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">👥 Connections</h5>
                    <h3 class="card-text">{{ Auth::user()->connections()->where('status', 'accepted')->count() }}</h3>
                    <a href="{{ route('connections.index') }}" class="btn btn-sm btn-outline-success">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-warning">📬 Pending</h5>
                    <h3 class="card-text">{{ Auth::user()->receivedInvitations()->where('status', 'pending')->count() }}</h3>
                    <a href="{{ route('invitations.pending') }}" class="btn btn-sm btn-outline-warning">Review</a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-info">🤖 AI Posts</h5>
                    <h3 class="card-text">{{ Auth::user()->posts()->where('is_ai_generated', true)->count() }}</h3>
                    <a href="{{ route('posts.ai.generate') }}" class="btn btn-sm btn-outline-info">Generate</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <!-- Recent Posts -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📰 Recent Posts from Your Network</h5>
                    <a href="{{ route('posts.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @php
                        $recentPosts = \App\Models\Post::with('user')
                            ->forConnectedUsers(Auth::id())
                            ->visible()
                            ->latest()
                            ->limit(5)
                            ->get();
                    @endphp

                    @forelse($recentPosts as $post)
                        <div class="d-flex mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    {{ substr($post->user->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">{{ $post->title }}</h6>
                                    <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 text-muted small">{{ Str::limit($post->content, 100) }}</p>
                                <small class="text-muted">
                                    by {{ $post->user->name }}
                                    @if($post->is_ai_generated)
                                        <span class="badge bg-info ms-1">🤖 AI</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <p>No posts from your network yet.</p>
                            <a href="{{ route('connections.suggestions') }}" class="btn btn-sm btn-outline-primary">Find Connections</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">⚡ Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('posts.create') }}" class="btn btn-primary">
                            ✍️ Create New Post
                        </a>
                        <a href="{{ route('posts.ai.generate') }}" class="btn btn-info">
                            🤖 Generate AI Post
                        </a>
                        <a href="{{ route('invitations.create') }}" class="btn btn-success">
                            📨 Send Invitation
                        </a>
                        <a href="{{ route('connections.suggestions') }}" class="btn btn-outline-primary">
                            👥 Find People
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pending Invitations -->
            @php
                $pendingInvitations = Auth::user()->receivedInvitations()
                    ->where('status', 'pending')
                    ->with('sender')
                    ->latest()
                    ->limit(3)
                    ->get();
            @endphp

            @if($pendingInvitations->count() > 0)
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">📬 Pending Invitations</h6>
                        <a href="{{ route('invitations.pending') }}" class="btn btn-sm btn-outline-warning">View All</a>
                    </div>
                    <div class="card-body">
                        @foreach($pendingInvitations as $invitation)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong>{{ $invitation->sender->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $invitation->created_at->diffForHumans() }}</small>
                                </div>
                                <div>
                                    <form method="POST" action="{{ route('invitations.accept', $invitation) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">✓</button>
                                    </form>
                                    <form method="POST" action="{{ route('invitations.reject', $invitation) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">✗</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>