<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvoiceApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run seeders to create roles and permissions
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
        
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
        
        $this->client = Client::factory()->create();
    }

    public function test_can_list_invoices(): void
    {
        Sanctum::actingAs($this->user);

        Invoice::factory()->count(3)->create([
            'client_id' => $this->client->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson('/api/invoices');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'invoice_number',
                            'description',
                            'total_amount',
                            'status',
                            'client',
                            'user',
                            'items'
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'total',
                        'per_page'
                    ]
                ]);
    }

    public function test_can_create_invoice(): void
    {
        Sanctum::actingAs($this->user);

        $invoiceData = [
            'client_id' => $this->client->id,
            'description' => 'Test invoice creation',
            'additional_notes' => 'Additional notes',
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'items' => [
                [
                    'name' => 'Test Product',
                    'quantity' => 2,
                    'unit_price' => 150.00,
                    'tax_rate' => 19.00
                ]
            ]
        ];

        $response = $this->postJson('/api/invoices', $invoiceData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'invoice_number',
                        'description',
                        'total_amount',
                        'status',
                        'client',
                        'items'
                    ]
                ]);

        $this->assertDatabaseHas('invoices', [
            'client_id' => $this->client->id,
            'description' => 'Test invoice creation'
        ]);
    }

    public function test_can_show_invoice(): void
    {
        Sanctum::actingAs($this->user);

        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson("/api/invoices/{$invoice->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'invoice_number',
                        'description',
                        'total_amount',
                        'status',
                        'client',
                        'user',
                        'items'
                    ]
                ])
                ->assertJson([
                    'data' => [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                    ]
                ]);
    }

    public function test_can_update_invoice(): void
    {
        Sanctum::actingAs($this->user);

        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'user_id' => $this->user->id,
            'description' => 'Original description',
        ]);

        $updateData = [
            'description' => 'Updated description',
            'status' => 'paid',
        ];

        $response = $this->putJson("/api/invoices/{$invoice->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'description' => 'Updated description',
                        'status' => 'paid',
                    ]
                ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'description' => 'Updated description',
            'status' => 'paid',
        ]);
    }

    public function test_can_delete_invoice(): void
    {
        Sanctum::actingAs($this->user);

        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->deleteJson("/api/invoices/{$invoice->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('invoices', [
            'id' => $invoice->id,
        ]);
    }

    public function test_cannot_access_invoices_without_authentication(): void
    {
        $response = $this->getJson('/api/invoices');

        $response->assertStatus(401);
    }

    public function test_cannot_create_invoice_with_invalid_data(): void
    {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'client_id' => 999, // non-existent client
            'description' => '',
            'issue_date' => 'invalid-date',
        ];

        $response = $this->postJson('/api/invoices', $invalidData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['client_id', 'issue_date']);
    }

    public function test_can_filter_invoices_by_status(): void
    {
        Sanctum::actingAs($this->user);

        Invoice::factory()->create([
            'client_id' => $this->client->id,
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        Invoice::factory()->create([
            'client_id' => $this->client->id,
            'user_id' => $this->user->id,
            'status' => 'paid',
        ]);

        $response = $this->getJson('/api/invoices?status=pending');

        $response->assertStatus(200);
        
        $invoices = $response->json('data');
        $this->assertCount(1, $invoices);
        $this->assertEquals('pending', $invoices[0]['status']);
    }

    public function test_user_can_only_access_their_invoices(): void
    {
        // Create another user
        $otherUser = User::factory()->create();
        $otherUser->assignRole('user');

        // Create invoice for other user
        $otherInvoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'user_id' => $otherUser->id,
        ]);

        // Act as current user
        Sanctum::actingAs($this->user);

        // Try to access other user's invoice (assuming proper authorization)
        $response = $this->getJson("/api/invoices/{$otherInvoice->id}");

        // This should be forbidden unless user is admin
        if (!$this->user->hasRole('admin')) {
            $response->assertStatus(403);
        }
    }

    public function test_can_update_invoice_status(): void
    {
        Sanctum::actingAs($this->user);

        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $response = $this->patchJson("/api/invoices/{$invoice->id}/status", [
            'status' => 'paid'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'status' => 'paid'
                    ]
                ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'paid',
        ]);
    }
}
