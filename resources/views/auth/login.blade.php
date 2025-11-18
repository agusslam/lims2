<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - LIMS</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Nunito', sans-serif;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 2px solid #e3e6f0;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-8 col-md-9">
                    <div class="card login-card">
                        <div class="login-header">
                            <div class="mb-3">
                                <i class="fas fa-flask fa-3x"></i>
                            </div>
                            <h2 class="mb-2">LIMS System</h2>
                            <p class="mb-0">Laboratory Information Management System</p>
                        </div>
                        
                        <div class="login-body">
                            @if(session('success'))
                                <div class="alert alert-success mb-4">
                                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger mb-4">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    @foreach($errors->all() as $error)
                                        {{ $error }}<br>
                                    @endforeach
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                
                                <div class="form-group">
                                    <label for="email" class="font-weight-bold">
                                        <i class="fas fa-envelope me-2"></i>Email Address
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required 
                                           autocomplete="email" 
                                           autofocus 
                                           placeholder="Enter your email address">
                                </div>

                                <div class="form-group">
                                    <label for="password" class="font-weight-bold">
                                        <i class="fas fa-lock me-2"></i>Password
                                    </label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           autocomplete="current-password"
                                           placeholder="Enter your password">
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="remember">
                                            Remember Me
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <button type="submit" class="btn btn-primary btn-login btn-block">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login to LIMS
                                    </button>
                                </div>
                            </form>

                            <hr>
                            
                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Contact your administrator for account access
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Demo Accounts Info -->
                    <div class="card mt-4" style="background: rgba(255, 255, 255, 0.9);">
                        <div class="card-body">
                            <h6 class="card-title text-center mb-3">
                                <i class="fas fa-users me-2"></i>Demo Accounts
                            </h6>
                            <div class="row text-sm">
                                <div class="col-md-6">
                                    <strong>Administrator:</strong><br>
                                    Email: admin@lims.com<br>
                                    Password: admin123<br><br>
                                    
                                    <strong>Developer:</strong><br>
                                    Email: pandu@lims.com<br>
                                    Password: dev123456<br>
                                </div>
                                <div class="col-md-6">
                                    <strong>Analyst:</strong><br>
                                    Email: aryo@lims.com<br>
                                    Password: analyst123<br><br>
                                    
                                    <strong>Supervisor:</strong><br>
                                    Email: trihandoyo@lims.com<br>
                                    Password: supervisor123<br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
