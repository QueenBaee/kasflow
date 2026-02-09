<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_daily_report(): void
    {
        $owner = User::factory()->create();
        
        $store = Store::create([
            'owner_id' => $owner->id,
            'name' => 'Test Store',
        ]);
        
        $store->users()->attach($owner->id, ['role' => 'owner']);

        Transaction::create([
            'store_id' => $store->id,
            'user_id' => $owner->id,
            'type' => 'income',
            'amount' => 100000,
            'transaction_date' => now(),
        ]);

        Transaction::create([
            'store_id' => $store->id,
            'user_id' => $owner->id,
            'type' => 'expense',
            'amount' => 30000,
            'transaction_date' => now(),
        ]);

        $response = $this->actingAs($owner, 'sanctum')
            ->getJson("/api/stores/{$store->id}/reports/daily?date=" . now()->format('Y-m-d'));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'total_income' => 100000,
                    'total_expense' => 30000,
                    'profit' => 70000,
                ],
            ]);
    }

    public function test_cashier_cannot_view_reports(): void
    {
        $owner = User::factory()->create();
        $cashier = User::factory()->create();
        
        $store = Store::create([
            'owner_id' => $owner->id,
            'name' => 'Test Store',
        ]);
        
        $store->users()->attach($cashier->id, ['role' => 'cashier']);

        $response = $this->actingAs($cashier, 'sanctum')
            ->getJson("/api/stores/{$store->id}/reports/daily?date=" . now()->format('Y-m-d'));

        $response->assertStatus(403);
    }

    public function test_monthly_report_calculates_correctly(): void
    {
        $owner = User::factory()->create();
        
        $store = Store::create([
            'owner_id' => $owner->id,
            'name' => 'Test Store',
        ]);
        
        $store->users()->attach($owner->id, ['role' => 'owner']);

        Transaction::create([
            'store_id' => $store->id,
            'user_id' => $owner->id,
            'type' => 'income',
            'amount' => 500000,
            'transaction_date' => now()->startOfMonth(),
        ]);

        Transaction::create([
            'store_id' => $store->id,
            'user_id' => $owner->id,
            'type' => 'expense',
            'amount' => 200000,
            'transaction_date' => now()->startOfMonth(),
        ]);

        $response = $this->actingAs($owner, 'sanctum')
            ->getJson("/api/stores/{$store->id}/reports/monthly?month=" . now()->month . "&year=" . now()->year);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'total_income' => 500000,
                    'total_expense' => 200000,
                    'profit' => 300000,
                ],
            ]);
    }
}
