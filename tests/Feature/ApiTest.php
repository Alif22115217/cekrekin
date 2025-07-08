<?php

use Tests\TestCase;

class AlatApiTest extends TestCase
{
    public function testShowAllAlat()
    {
        // Kirim request GET ke /api/v1/alat
        $response = $this->json('GET', 'http://127.0.0.1:8000/api/v1/alat');

        // Cek jika status code 200 (OK)
        $response->assertStatus(200);

        // Cek jika response JSON memiliki field 'message' dan 'data'
        $response->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'kategori_id',
                    'nama_alat',
                    'harga24',
                    'harga12',
                    'harga6',
                    'nama_kategori',
                ]
            ]
        ]);
    }

    public function testCreateAlat()
    {
        // Data alat yang akan dibuat
        $data = [
            'nama_alat' => 'Alat Baru',
            'kategori_id' => 1,  // Asumsi kategori dengan ID 1 sudah ada
            'harga24' => 150000,
            'harga12' => 90000,
            'harga6' => 45000,
        ];

        // Kirim request POST ke /api/v1/alat untuk menambah alat baru
        $response = $this->json('POST', '/api/v1/alat', $data);

        // Cek jika status code adalah 201 (Created)
        $response->assertStatus(201);

        // Cek jika response JSON mengandung pesan bahwa alat berhasil ditambahkan
        $response->assertJsonFragment(['message' => 'Alat added successfully']);
    }
}