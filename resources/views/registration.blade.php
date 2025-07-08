<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <title>Registrasi</title>
    </head>
    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container px-4">
                <a class="navbar-brand" href="{{ route('home') }}">Rental Kamera Amikom</a>
            </div>
        </nav>

        <div class="container mt-4 px-3">
            <div class="row justify-content-center">
                <div class="col col-md-5">
                    <form action="{{ route('register.store') }}" method="POST">
                        @csrf

                        <!-- Nama -->
                        <div class="form-floating mb-2">
                            <input type="text" name="name" class="form-control" id="floatingName" placeholder="Nama" value="{{ old('name') }}" required>
                            <label for="floatingName">Nama Lengkap</label>
                        </div>
                        @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <!-- Email -->
                        <div class="form-floating mb-2">
                            <input type="email" name="email" class="form-control" id="floatingEmail" placeholder="name@example.com" value="{{ old('email') }}" required>
                            <label for="floatingEmail">Email</label>
                        </div>
                        @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <!-- Password -->
                        <div class="form-floating mb-2">
                            <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                            <label for="floatingPassword">Password</label>
                        </div>
                        @error('password')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <!-- Konfirmasi Password -->
                        <div class="form-floating mb-2">
                            <input type="password" name="password_confirmation" class="form-control" id="floatingPasswordConfirmation" placeholder="Konfirmasi Password" required>
                            <label for="floatingPasswordConfirmation">Konfirmasi Password</label>
                        </div>
                        @error('password_confirmation')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <!-- Telepon -->
                        <small class="text-muted">No Telepon disarankan yang terhubung dengan WhatsApp.</small>
                        <div class="input-group mb-3">
                            <label class="input-group-text" for="telepon_kode">Kode</label>
                            <select name="telepon_kode" class="form-select" id="telepon_kode" required>
                                <option value="">Pilih</option>
                                <option value="+62" {{ old('telepon_kode') == '+62' ? 'selected' : '' }}>🇮🇩 +62 (Indonesia)</option>
                                <option value="+60" {{ old('telepon_kode') == '+60' ? 'selected' : '' }}>🇲🇾 +60 (Malaysia)</option>
                                <option value="+1"  {{ old('telepon_kode') == '+1'  ? 'selected' : '' }}>🇺🇸 +1 (USA)</option>
                                <option value="+91" {{ old('telepon_kode') == '+91' ? 'selected' : '' }}>🇮🇳 +91 (India)</option>
                                <option value="+81" {{ old('telepon_kode') == '+81' ? 'selected' : '' }}>🇯🇵 +81 (Jepang)</option>
                            </select>
                            <input type="text" name="telepon" class="form-control" placeholder="81234567890" value="{{ old('telepon') }}" required>
                        </div>
                        @error('telepon')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <!-- Tombol Submit -->
                        <button type="submit" class="btn btn-success w-100 mt-4">Daftar</button>
                    </form>

                    <!-- Link login -->
                    <div class="d-flex w-100 mt-3">
                        Sudah punya akun? Silakan&nbsp;<a class="link-dark" href="{{ route('home') }}">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
