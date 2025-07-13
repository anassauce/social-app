<x-app-layout>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="card-title">{{ $post->title }}</h1>
                            <div class="d-flex align-items-center text-muted mb-2">
                                <small>
                                    By <strong>{{ $post->user->name }}</strong> • 
                                    {{ $post->created_at->format('M j, Y \a\t g:i A') }}
                                    @if($post->is_ai_generated)
                                        <span class="badge bg-info ms-2"><i class="bi bi-gear me-1"></i>AI Generated</span>
                                    @endif
                                </small>
                            </div>
                            <div class="mb-3">
                                <span class="badge bg-secondary">{{ ucfirst($post->visibility) }}</span>
                                <span class="badge bg-{{ $post->status === 'published' ? 'success' : 'warning' }}">{{ ucfirst($post->status) }}</span>
                            </div>
                        </div>
                        
                        @if($post->user_id === Auth::id())
                            <div class="btn-group" role="group">
                                <a href="{{ route('posts.edit', $post) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-pen me-1"></i>Edit
                                </a>
                                <form method="POST" action="{{ route('posts.destroy', $post) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-x me-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    
                    <div class="card-text">
                        <div style="white-space: pre-wrap; line-height: 1.6;">{{ $post->content }}</div>
                    </div>
                    
                    @if($post->is_ai_generated && $post->ai_prompt)
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="text-muted mb-2"><i class="bi bi-gear me-1"></i>AI Prompt:</h6>
                            <small class="text-muted">{{ $post->ai_prompt }}</small>
                        </div>
                    @endif
                    
                    <!-- Like and Comment Section -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center mb-3">
                            <button class="btn btn-outline-primary btn-sm me-2" id="likeBtn" onclick="toggleLike({{ $post->id }})">
                                <i class="bi bi-heart{{ $isLiked ? '-fill' : '' }}"></i>
                                <span id="likeText">{{ $isLiked ? 'Unlike' : 'Like' }}</span>
                            </button>
                            <span id="likesCount" class="text-muted me-3">{{ $likesCount }} {{ $likesCount == 1 ? 'like' : 'likes' }}</span>
                            <span class="text-muted">{{ $post->comments->count() }} {{ $post->comments->count() == 1 ? 'comment' : 'comments' }}</span>
                        </div>
                        
                        <!-- Comment Form -->
                        <form id="commentForm" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <textarea class="form-control" id="commentContent" rows="3" placeholder="Write a comment..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-chat me-1"></i>Post Comment
                            </button>
                        </form>
                        
                        <!-- Comments List -->
                        <div id="commentsList">
                            @foreach($post->comments->whereNull('parent_id') as $comment)
                                <div class="comment mb-3" data-comment-id="{{ $comment->id }}">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <div class="bg-light p-3 rounded">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <strong>{{ $comment->user->name }}</strong>
                                                    <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                                </div>
                                                <p class="mb-0 mt-1">{{ $comment->content }}</p>
                                            </div>
                                            
                                            <!-- Reply Button -->
                                            <button class="btn btn-sm btn-link text-muted reply-btn" onclick="showReplyForm({{ $comment->id }})">
                                                <i class="bi bi-arrow-return-right me-1"></i>Reply
                                            </button>
                                            
                                            <!-- Reply Form (hidden by default) -->
                                            <div class="reply-form mt-2" id="replyForm{{ $comment->id }}" style="display: none;">
                                                <form onsubmit="submitReply(event, {{ $comment->id }})">
                                                    @csrf
                                                    <div class="mb-2">
                                                        <textarea class="form-control" rows="2" placeholder="Write a reply..." required></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-arrow-return-right me-1"></i>Reply
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-secondary" onclick="hideReplyForm({{ $comment->id }})">
                                                        <i class="bi bi-x me-1"></i>Cancel
                                                    </button>
                                                </form>
                                            </div>
                                            
                                            <!-- Replies -->
                                            @foreach($comment->replies as $reply)
                                                <div class="ms-4 mt-2">
                                                    <div class="bg-light p-2 rounded">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <strong>{{ $reply->user->name }}</strong>
                                                            <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                                        </div>
                                                        <p class="mb-0 mt-1">{{ $reply->content }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    Created: {{ $post->created_at->format('M j, Y \a\t g:i A') }}
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    @if($post->updated_at != $post->created_at)
                                        Last updated: {{ $post->updated_at->format('M j, Y \a\t g:i A') }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Posts
                </a>
                @if($post->user_id === Auth::id())
                    <a href="{{ route('posts.my-posts') }}" class="btn btn-outline-primary">
                        <i class="bi bi-person me-1"></i>My Posts
                    </a>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Get CSRF token
        function getCSRFToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                   document.querySelector('input[name="_token"]')?.value;
        }

        // Toggle like functionality
        function toggleLike(postId) {
            const csrfToken = getCSRFToken();
            
            fetch(`/posts/${postId}/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        throw new Error(data.error || `HTTP error! status: ${response.status}`);
                    }
                    return data;
                });
            })
            .then(data => {
                const likeBtn = document.getElementById('likeBtn');
                const likeText = document.getElementById('likeText');
                const likesCount = document.getElementById('likesCount');
                const heartIcon = likeBtn.querySelector('i');
                
                if (data.liked) {
                    likeText.textContent = 'Unlike';
                    heartIcon.className = 'bi bi-heart-fill';
                    likeBtn.classList.remove('btn-outline-primary');
                    likeBtn.classList.add('btn-primary');
                } else {
                    likeText.textContent = 'Like';
                    heartIcon.className = 'bi bi-heart';
                    likeBtn.classList.remove('btn-primary');
                    likeBtn.classList.add('btn-outline-primary');
                }
                
                likesCount.textContent = `${data.likes_count} ${data.likes_count == 1 ? 'like' : 'likes'}`;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error liking post: ' + error.message);
            });
        }

        // Submit comment functionality
        document.getElementById('commentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const content = document.getElementById('commentContent').value;
            const postId = {{ $post->id }};
            const csrfToken = getCSRFToken();
            
            fetch(`/posts/${postId}/comment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    content: content
                })
            })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        throw new Error(data.error || `HTTP error! status: ${response.status}`);
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.success) {
                    // Add the new comment to the comments list
                    const commentsList = document.getElementById('commentsList');
                    const newComment = document.createElement('div');
                    newComment.className = 'comment mb-3';
                    newComment.innerHTML = `
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <div class="bg-light p-3 rounded">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <strong>${data.comment.user.name}</strong>
                                        <small class="text-muted">just now</small>
                                    </div>
                                    <p class="mb-0 mt-1">${data.comment.content}</p>
                                </div>
                                <button class="btn btn-sm btn-link text-muted reply-btn" onclick="showReplyForm(${data.comment.id})">
                                    Reply
                                </button>
                            </div>
                        </div>
                    `;
                    commentsList.appendChild(newComment);
                    
                    // Clear the form
                    document.getElementById('commentContent').value = '';
                    
                    // Update comment count
                    location.reload(); // Simple reload to update comment count
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error posting comment: ' + error.message);
            });
        });

        // Show reply form
        function showReplyForm(commentId) {
            document.getElementById(`replyForm${commentId}`).style.display = 'block';
        }

        // Hide reply form
        function hideReplyForm(commentId) {
            document.getElementById(`replyForm${commentId}`).style.display = 'none';
        }

        // Submit reply
        function submitReply(event, parentId) {
            event.preventDefault();
            
            const form = event.target;
            const content = form.querySelector('textarea').value;
            const postId = {{ $post->id }};
            const csrfToken = getCSRFToken();
            
            fetch(`/posts/${postId}/comment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    content: content,
                    parent_id: parentId
                })
            })
            .then(response => {
                return response.json().then(data => {
                    if (!response.ok) {
                        throw new Error(data.error || `HTTP error! status: ${response.status}`);
                    }
                    return data;
                });
            })
            .then(data => {
                if (data.success) {
                    // Reload page to show new reply
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error posting reply: ' + error.message);
            });
        }
    </script>
</x-app-layout>