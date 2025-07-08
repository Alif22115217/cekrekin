<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | Rental Kamera Amikom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional: Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Styles (optional) -->
    <style>
        body {
            background: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .btn-primary {
            background-color: #3c4cad;
            border-color: #3c4cad;
        }

        .form-label {
            font-weight: 600;
        }

        .alert {
            margin-bottom: 20px;
        }

        .container {
            padding: 50px 15px;
        }

        .text-muted {
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card p-4">
                <div class="card-body">
                    <h4 class="mb-4 text-center"><i class="fas fa-unlock-alt me-2"></i>Reset Password</h4>

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Anda</label>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="Masukkan email terdaftar" value="{{ old('email') }}">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Minimal 6 karakter">
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-key me-2"></i>Reset Password
                        </button>
                    </form>
                </div>
            </div>
            <p class="text-center mt-3 text-muted">&copy; {{ date('Y') }} Rental Kamera Amikom</p>
        </div>
    </div>
</div>

<!-- Bootstrap JS (optional if already included globally) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
