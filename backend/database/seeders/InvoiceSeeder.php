<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $clients = Client::all();

        if ($users->isEmpty() || $clients->isEmpty()) {
            $this->command->warn('Users or Clients not found. Please run UserSeeder and ClientSeeder first.');
            return;
        }

        $invoices = [
            [
                'invoice_number' => 'INV-2024-001',
                'client_id' => $clients[0]->id,
                'user_id' => $users[1]->id,
                'description' => 'Desarrollo de aplicación web',
                'additional_notes' => 'Proyecto completado según especificaciones',
                'issue_date' => Carbon::now()->subDays(30),
                'due_date' => Carbon::now()->subDays(15),
                'status' => 'paid',
                'items' => [
                    [
                        'name' => 'Desarrollo Frontend',
                        'quantity' => 1,
                        'unit_price' => 2500000,
                        'tax_rate' => 19.00,
                    ],
                    [
                        'name' => 'Desarrollo Backend',
                        'quantity' => 1,
                        'unit_price' => 3000000,
                        'tax_rate' => 19.00,
                    ],
                ]
            ],
            [
                'invoice_number' => 'INV-2024-002',
                'client_id' => $clients[1]->id,
                'user_id' => $users[1]->id,
                'description' => 'Consultoría en sistemas',
                'additional_notes' => null,
                'issue_date' => Carbon::now()->subDays(20),
                'due_date' => Carbon::now()->addDays(10),
                'status' => 'pending',
                'items' => [
                    [
                        'name' => 'Consultoría técnica',
                        'quantity' => 40,
                        'unit_price' => 150000,
                        'tax_rate' => 19.00,
                    ],
                ]
            ],
            [
                'invoice_number' => 'INV-2024-003',
                'client_id' => $clients[2]->id,
                'user_id' => $users[2]->id,
                'description' => 'Mantenimiento de servidores',
                'additional_notes' => 'Mantenimiento mensual programado',
                'issue_date' => Carbon::now()->subDays(45),
                'due_date' => Carbon::now()->subDays(15),
                'status' => 'overdue',
                'items' => [
                    [
                        'name' => 'Mantenimiento servidor principal',
                        'quantity' => 1,
                        'unit_price' => 800000,
                        'tax_rate' => 19.00,
                    ],
                    [
                        'name' => 'Backup y respaldo',
                        'quantity' => 1,
                        'unit_price' => 200000,
                        'tax_rate' => 19.00,
                    ],
                ]
            ],
            [
                'invoice_number' => 'INV-2024-004',
                'client_id' => $clients[3]->id,
                'user_id' => $users[1]->id,
                'description' => 'Diseño de interfaz de usuario',
                'additional_notes' => 'Diseño responsive para múltiples dispositivos',
                'issue_date' => Carbon::now()->subDays(10),
                'due_date' => Carbon::now()->addDays(20),
                'status' => 'pending',
                'items' => [
                    [
                        'name' => 'Diseño UI/UX',
                        'quantity' => 1,
                        'unit_price' => 1500000,
                        'tax_rate' => 19.00,
                    ],
                    [
                        'name' => 'Prototipado',
                        'quantity' => 3,
                        'unit_price' => 300000,
                        'tax_rate' => 19.00,
                    ],
                ]
            ],
            [
                'invoice_number' => 'INV-2024-005',
                'client_id' => $clients[4]->id,
                'user_id' => $users[2]->id,
                'description' => 'Implementación de API REST',
                'additional_notes' => 'API con documentación completa',
                'issue_date' => Carbon::now()->subDays(5),
                'due_date' => Carbon::now()->addDays(25),
                'status' => 'pending',
                'items' => [
                    [
                        'name' => 'Desarrollo API',
                        'quantity' => 1,
                        'unit_price' => 4000000,
                        'tax_rate' => 19.00,
                    ],
                    [
                        'name' => 'Documentación',
                        'quantity' => 1,
                        'unit_price' => 500000,
                        'tax_rate' => 19.00,
                    ],
                    [
                        'name' => 'Testing',
                        'quantity' => 20,
                        'unit_price' => 100000,
                        'tax_rate' => 19.00,
                    ],
                ]
            ],
        ];

        foreach ($invoices as $invoiceData) {
            $items = $invoiceData['items'];
            unset($invoiceData['items']);

            $totalAmount = 0;
            foreach ($items as $item) {
                $subtotal = $item['unit_price'] * $item['quantity'];
                $taxAmount = $subtotal * ($item['tax_rate'] / 100);
                $totalAmount += $subtotal + $taxAmount;
            }

            $invoiceData['total_amount'] = $totalAmount;

            $invoice = Invoice::create($invoiceData);

            foreach ($items as $item) {
                $subtotal = $item['unit_price'] * $item['quantity'];
                $taxAmount = $subtotal * ($item['tax_rate'] / 100);
                $itemTotal = $subtotal + $taxAmount;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => $taxAmount,
                    'total_amount' => $itemTotal,
                ]);
            }
        }
    }
}