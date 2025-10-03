<?php

namespace Tests\Unit;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use App\Services\CacheService;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    private InvoiceService $invoiceService;
    private CacheService $cacheService;
    private User $user;
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->cacheService = $this->app->make(CacheService::class);
        $this->invoiceService = new InvoiceService($this->cacheService);
        
        // Create test user and client
        $this->user = User::factory()->create();
        $this->client = Client::factory()->create();
    }

    public function test_can_create_invoice(): void
    {
        $invoiceData = [
            'client_id' => $this->client->id,
            'user_id' => $this->user->id,
            'description' => 'Test invoice',
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'items' => [
                [
                    'name' => 'Test item',
                    'quantity' => 2,
                    'unit_price' => 100.00,
                    'tax_rate' => 19.00
                ]
            ]
        ];

        $invoice = $this->invoiceService->createInvoice($invoiceData);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals($this->client->id, $invoice->client_id);
        $this->assertEquals($this->user->id, $invoice->user_id);
        $this->assertEquals('Test invoice', $invoice->description);
        $this->assertEquals(238.00, $invoice->total_amount); // 200 + 19% tax
        $this->assertCount(1, $invoice->items);
    }

    public function test_can_calculate_invoice_total(): void
    {
        $items = [
            [
                'unit_price' => 100.00,
                'quantity' => 2,
                'tax_rate' => 19.00
            ],
            [
                'unit_price' => 50.00,
                'quantity' => 1,
                'tax_rate' => 19.00
            ]
        ];

        $total = $this->invoiceService->calculateInvoiceTotal($items);

        // (100 * 2) + (100 * 2 * 0.19) + (50 * 1) + (50 * 1 * 0.19) = 200 + 38 + 50 + 9.5 = 297.5
        $this->assertEquals(297.50, $total);
    }

    public function test_generates_unique_invoice_numbers(): void
    {
        $invoice1 = $this->invoiceService->createInvoice([
            'client_id' => $this->client->id,
            'user_id' => $this->user->id,
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
        ]);

        $invoice2 = $this->invoiceService->createInvoice([
            'client_id' => $this->client->id,
            'user_id' => $this->user->id,
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
        ]);

        $this->assertNotEquals($invoice1->invoice_number, $invoice2->invoice_number);
        $this->assertStringStartsWith('INV-', $invoice1->invoice_number);
        $this->assertStringStartsWith('INV-', $invoice2->invoice_number);
    }
}
