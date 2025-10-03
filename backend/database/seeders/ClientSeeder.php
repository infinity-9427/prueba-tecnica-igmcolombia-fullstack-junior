<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'first_name' => 'Carlos',
                'last_name' => 'Rodriguez',
                'document_type' => 'cedula',
                'document_number' => '12345678',
                'email' => 'carlos@example.com',
                'phone' => '+57 300 123 4567',
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Garcia',
                'document_type' => 'cedula',
                'document_number' => '87654321',
                'email' => 'maria@example.com',
                'phone' => '+57 301 234 5678',
            ],
            [
                'first_name' => 'Tech Solutions',
                'last_name' => 'SAS',
                'document_type' => 'nit',
                'document_number' => '900123456-1',
                'email' => 'contact@techsolutions.com',
                'phone' => '+57 302 345 6789',
            ],
            [
                'first_name' => 'Ana',
                'last_name' => 'Lopez',
                'document_type' => 'cedula',
                'document_number' => '11223344',
                'email' => 'ana@example.com',
                'phone' => '+57 303 456 7890',
            ],
            [
                'first_name' => 'Global Corp',
                'last_name' => 'Inc',
                'document_type' => 'nit',
                'document_number' => '800987654-3',
                'email' => 'info@globalcorp.com',
                'phone' => '+57 304 567 8901',
            ],
        ];

        foreach ($clients as $clientData) {
            Client::create($clientData);
        }
    }
}