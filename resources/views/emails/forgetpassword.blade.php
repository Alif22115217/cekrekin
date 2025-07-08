<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
            margin: 0;
        }
        .email-container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #343a40;
            text-align: center;
        }
        p {
            color: #343a40;
            font-size: 16px;
            line-height: 1.6;
        }
        .cta-button {
            background-color: #0d6efd;
            color: #ffffff;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
        }
        .cta-button:hover {
            background-color: #0b5ed7;
        }
        .footer {
            color: #6c757d;
            font-size: 13px;
            text-align: center;
            margin-top: 40px;
        }
        .footer hr {
            margin-top: 20px;
            border: none;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h2>Reset Password Akun Anda</h2>

        <p>Halo, {{ $user->name }},</p>

        <p>Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('reset-password/'.$token) }}">Reset Password Sekarang</a>
        </div>

        <p>Tautan di atas hanya berlaku selama 60 menit. Jika Anda tidak meminta reset password, abaikan email ini.</p>

        <hr>

        <p class="footer">&copy; {{ date('Y') }} Rental Kamera Amikom Yogyakarta</p>
    </div>
</body>
</html>
