<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions; // Import DatabaseTransactions
use Tests\TestCase;

class SignUpTest extends TestCase
{
    use DatabaseTransactions; // Menggunakan DatabaseTransactions trait

    /** @test */
    public function user_can_register_with_valid_data()
    {
        // Data valid untuk pendaftaran
        $data = [
            'email' => 'aslam@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Kirim POST request untuk registrasi
        $response = $this->post(route('register.store'), $data);

        // Pastikan pengguna berhasil didaftarkan dan diarahkan ke halaman yang benar
        $response->assertRedirect(route('home'));

        // Verifikasi bahwa pengguna benar-benar terdaftar di database
        $this->assertDatabaseHas('users', [
            'email' => 'aslam@test.com', // Memastikan email sesuai
        ]);
    }


    /** @test */
    public function registration_fails_with_invalid_email_format()
    {
        // Data dengan format email yang salah
        $data = [
            'email' => 'invalidemail',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Kirim POST request untuk registrasi
        $response = $this->post(route('register.store'), $data);

        // Pastikan sistem mengembalikan error untuk email
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function registration_fails_if_passwords_do_not_match()
    {
        // Data dengan password yang tidak cocok
        $data = [
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ];

        // Kirim POST request untuk registrasi
        $response = $this->post(route('register.store'), $data);

        // Pastikan sistem mengembalikan error untuk konfirmasi password
        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function registration_fails_if_email_is_already_taken()
    {
        // Membuat pengguna dengan email yang sudah ada
        $existingUser = User::factory()->create([
            'email' => 'testuser@example.com',
        ]);

        // Data pendaftaran dengan email yang sudah ada
        $data = [
            'email' => 'testuser@example.com', // Email yang sudah digunakan
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Kirim POST request untuk registrasi
        $response = $this->post(route('register.store'), $data);

        // Pastikan sistem mengembalikan error bahwa email sudah digunakan
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function registration_fails_if_required_fields_are_missing()
    {
        // Data dengan field yang hilang (email dan password)
        $data = [];

        // Kirim POST request untuk registrasi
        $response = $this->post(route('register.store'), $data);

        // Pastikan sistem mengembalikan error untuk email dan password
        $response->assertSessionHasErrors(['email', 'password']);
    }
}