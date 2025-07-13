<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - AI-Powered Social Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: white;
        }
        
        .feature-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        
        .btn-outline-light:hover {
            background: rgba(255,255,255,0.2);
            border-color: white;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark position-absolute w-100" style="z-index: 1000;">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-grid-3x3-gap me-2"></i>{{ config('app.name') }}
            </a>
            
            <div class="navbar-nav ms-auto">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light me-2">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-light text-dark">Get Started</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        <i class="bi bi-grid-3x3-gap me-3"></i>Connect. Create. 
                        <span class="d-block">Collaborate with AI.</span>
                    </h1>
                    <p class="lead mb-4">
                        Join the future of professional networking where artificial intelligence meets human creativity. 
                        Build meaningful business connections and generate amazing content powered by AI.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-light btn-lg text-dark">
                                <i class="bi bi-arrow-right me-2"></i>Start Your Journey
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-person me-2"></i>Sign In
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg text-dark">
                                <i class="bi bi-grid me-2"></i>Go to Dashboard
                            </a>
                            <a href="{{ route('posts.create') }}" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-plus me-2"></i>Create Post
                            </a>
                        @endguest
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="display-1 mb-4">🤖🌐✨</div>
                    <h3>AI-Powered Social Platform</h3>
                    <p class="opacity-75">Where technology meets community</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold text-gradient">Powerful Features</h2>
                <p class="lead text-muted">Everything you need for modern social networking</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card card text-center p-4">
                        <div class="card-body">
                            <div class="display-4 mb-3">🤖</div>
                            <h5 class="card-title">AI Content Generation</h5>
                            <p class="card-text">
                                Generate engaging posts with advanced AI. Choose your style, 
                                tone, and length for perfect content every time.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card card text-center p-4">
                        <div class="card-body">
                            <div class="display-4 mb-3">👥</div>
                            <h5 class="card-title">Smart Connections</h5>
                            <p class="card-text">
                                Build meaningful relationships with invitation-based connections. 
                                Control who sees your content with privacy settings.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card card text-center p-4">
                        <div class="card-body">
                            <div class="display-4 mb-3">🔒</div>
                            <h5 class="card-title">Privacy Control</h5>
                            <p class="card-text">
                                Your data, your rules. Choose who can see your posts with 
                                granular privacy controls and connection management.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card card text-center p-4">
                        <div class="card-body">
                            <div class="display-4 mb-3">✨</div>
                            <h5 class="card-title">Content Enhancement</h5>
                            <p class="card-text">
                                Improve your existing content with AI. Fix grammar, 
                                enhance clarity, and boost engagement automatically.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card card text-center p-4">
                        <div class="card-body">
                            <div class="display-4 mb-3">📱</div>
                            <h5 class="card-title">Modern Interface</h5>
                            <p class="card-text">
                                Clean, intuitive design that works perfectly on all devices. 
                                Both web and API access for maximum flexibility.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card card text-center p-4">
                        <div class="card-body">
                            <div class="display-4 mb-3">⚡</div>
                            <h5 class="card-title">Lightning Fast</h5>
                            <p class="card-text">
                                Built with Laravel for speed and reliability. 
                                Real-time updates and seamless user experience.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container text-center text-white">
            <h2 class="display-5 fw-bold mb-4">Ready to Get Started?</h2>
            <p class="lead mb-4">
                Join thousands of users already creating amazing content with AI assistance.
            </p>
            @guest
                <a href="{{ route('register') }}" class="btn btn-light btn-lg text-dark me-3">
                    Create Free Account
                </a>
                <a href="#features" class="btn btn-outline-light btn-lg">
                    Learn More
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg text-dark me-3">
                    Go to Dashboard
                </a>
                <a href="{{ route('posts.ai.generate') }}" class="btn btn-outline-light btn-lg">
                    Generate AI Content
                </a>
            @endguest
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>💼 {{ config('app.name') }}</h5>
                    <p class="text-muted">AI-powered social platform for the future.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. Built with Laravel & AI.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>